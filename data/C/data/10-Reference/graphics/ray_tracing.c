/*
 * File: ray_tracing.c
 * Description: Simple ray tracing implementation
 */

#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <string.h>

#define WIDTH 80
#define HEIGHT 40
#define MAX_DEPTH 5
#define EPSILON 0.001

// 3D vector structure
typedef struct {
    double x, y, z;
} Vec3;

// Ray structure
typedef struct {
    Vec3 origin;
    Vec3 direction;
} Ray;

// Sphere structure
typedef struct {
    Vec3 center;
    double radius;
    int color;
} Sphere;

// Material structure
typedef struct {
    double diffuse;
    double specular;
    double reflection;
    int color;
} Material;

// Light structure
typedef struct {
    Vec3 position;
    Vec3 color;
    double intensity;
} Light;

// Vector operations
Vec3 vec3(double x, double y, double z) {
    Vec3 v = {x, y, z};
    return v;
}

Vec3 vec3_add(Vec3 a, Vec3 b) {
    return vec3(a.x + b.x, a.y + b.y, a.z + b.z);
}

Vec3 vec3_sub(Vec3 a, Vec3 b) {
    return vec3(a.x - b.x, a.y - b.y, a.z - b.z);
}

Vec3 vec3_mul(Vec3 v, double s) {
    return vec3(v.x * s, v.y * s, v.z * s);
}

Vec3 vec3_div(Vec3 v, double s) {
    return vec3(v.x / s, v.y / s, v.z / s);
}

double vec3_dot(Vec3 a, Vec3 b) {
    return a.x * b.x + a.y * b.y + a.z * b.z;
}

Vec3 vec3_cross(Vec3 a, Vec3 b) {
    return vec3(a.y * b.z - a.z * b.y,
                a.z * b.x - a.x * b.z,
                a.x * b.y - a.y * b.x);
}

double vec3_length(Vec3 v) {
    return sqrt(vec3_dot(v, v));
}

Vec3 vec3_normalize(Vec3 v) {
    double len = vec3_length(v);
    if (len > EPSILON) {
        return vec3_div(v, len);
    }
    return v;
}

// Ray-sphere intersection
int ray_sphere_intersect(Ray ray, Sphere sphere, double* t) {
    Vec3 oc = vec3_sub(ray.origin, sphere.center);
    double a = vec3_dot(ray.direction, ray.direction);
    double b = 2.0 * vec3_dot(oc, ray.direction);
    double c = vec3_dot(oc, oc) - sphere.radius * sphere.radius;
    
    double discriminant = b * b - 4 * a * c;
    
    if (discriminant < 0) {
        return 0;
    }
    
    double sqrt_discriminant = sqrt(discriminant);
    double t1 = (-b - sqrt_discriminant) / (2 * a);
    double t2 = (-b + sqrt_discriminant) / (2 * a);
    
    if (t1 > EPSILON) {
        *t = t1;
        return 1;
    }
    
    if (t2 > EPSILON) {
        *t = t2;
        return 1;
    }
    
    return 0;
}

// Calculate normal at intersection point
Vec3 get_normal(Sphere sphere, Vec3 point) {
    return vec3_normalize(vec3_sub(point, sphere.center));
}

// Reflect vector
Vec3 reflect(Vec3 v, Vec3 n) {
    return vec3_sub(v, vec3_mul(n, 2.0 * vec3_dot(v, n)));
}

// Calculate lighting
Vec3 calculate_lighting(Vec3 point, Vec3 normal, Vec3 view_dir, 
                      Material material, Light light, Sphere* spheres, int sphere_count) {
    Vec3 color = vec3(0, 0, 0);
    
    // Light direction
    Vec3 light_dir = vec3_normalize(vec3_sub(light.position, point));
    
    // Check if point is in shadow
    int in_shadow = 0;
    Ray shadow_ray = {point, light_dir};
    double shadow_t;
    
    for (int i = 0; i < sphere_count; i++) {
        if (ray_sphere_intersect(shadow_ray, spheres[i], &shadow_t)) {
            if (shadow_t < vec3_length(vec3_sub(light.position, point))) {
                in_shadow = 1;
                break;
            }
        }
    }
    
    if (!in_shadow) {
        // Diffuse lighting
        double diffuse_intensity = fmax(0.0, vec3_dot(normal, light_dir));
        Vec3 diffuse_color = vec3_mul(light.color, diffuse_intensity * light.intensity * material.diffuse);
        color = vec3_add(color, diffuse_color);
        
        // Specular lighting
        Vec3 reflect_dir = reflect(vec3_mul(light_dir, -1.0), normal);
        double specular_intensity = pow(fmax(0.0, vec3_dot(reflect_dir, view_dir)), 32.0);
        Vec3 specular_color = vec3_mul(light.color, specular_intensity * light.intensity * material.specular);
        color = vec3_add(color, specular_color);
    }
    
    return color;
}

// Trace ray
Vec3 trace_ray(Ray ray, Sphere* spheres, int sphere_count, Light light, int depth) {
    if (depth <= 0) {
        return vec3(0.1, 0.1, 0.1); // Background color
    }
    
    double closest_t = INFINITY;
    int closest_sphere = -1;
    
    // Find closest intersection
    for (int i = 0; i < sphere_count; i++) {
        double t;
        if (ray_sphere_intersect(ray, spheres[i], &t)) {
            if (t < closest_t) {
                closest_t = t;
                closest_sphere = i;
            }
        }
    }
    
    if (closest_sphere == -1) {
        return vec3(0.1, 0.1, 0.1); // Background color
    }
    
    // Calculate intersection point and normal
    Vec3 hit_point = vec3_add(ray.origin, vec3_mul(ray.direction, closest_t));
    Vec3 normal = get_normal(spheres[closest_sphere], hit_point);
    Vec3 view_dir = vec3_mul(ray.direction, -1.0);
    
    // Material properties
    Material material = {0.7, 0.3, 0.2, spheres[closest_sphere].color};
    
    // Calculate lighting
    Vec3 color = calculate_lighting(hit_point, normal, view_dir, material, light, spheres, sphere_count);
    
    // Reflection
    if (material.reflection > 0) {
        Vec3 reflect_dir = reflect(view_dir, normal);
        Ray reflect_ray = {vec3_add(hit_point, vec3_mul(normal, EPSILON)), reflect_dir};
        Vec3 reflect_color = trace_ray(reflect_ray, spheres, sphere_count, light, depth - 1);
        color = vec3_add(color, vec3_mul(reflect_color, material.reflection));
    }
    
    return color;
}

// Convert color to ASCII character
char color_to_ascii(Vec3 color) {
    double intensity = (color.x + color.y + color.z) / 3.0;
    
    if (intensity < 0.1) return ' ';
    if (intensity < 0.2) return '.';
    if (intensity < 0.3) return ':';
    if (intensity < 0.4) return '-';
    if (intensity < 0.5) return '=';
    if (intensity < 0.6) return '+';
    if (intensity < 0.7) return '*';
    if (intensity < 0.8) return '#';
    if (intensity < 0.9) return '%';
    return '@';
}

// Render scene
void render_scene(Sphere* spheres, int sphere_count, Light light) {
    char screen[HEIGHT][WIDTH];
    
    // Camera setup
    Vec3 camera_pos = vec3(0, 0, -5);
    Vec3 camera_target = vec3(0, 0, 0);
    Vec3 camera_up = vec3(0, 1, 0);
    
    // Calculate camera coordinate system
    Vec3 camera_forward = vec3_normalize(vec3_sub(camera_target, camera_pos));
    Vec3 camera_right = vec3_normalize(vec3_cross(camera_forward, camera_up));
    Vec3 camera_up_corrected = vec3_cross(camera_right, camera_forward);
    
    // Field of view
    double fov = 60.0 * M_PI / 180.0;
    double aspect_ratio = (double)WIDTH / HEIGHT;
    double half_height = tan(fov / 2.0);
    double half_width = aspect_ratio * half_height;
    
    // Render each pixel
    for (int y = 0; y < HEIGHT; y++) {
        for (int x = 0; x < WIDTH; x++) {
            // Calculate ray direction
            double u = (2.0 * x / WIDTH - 1.0) * half_width;
            double v = (1.0 - 2.0 * y / HEIGHT) * half_height;
            
            Vec3 ray_dir = vec3_normalize(
                vec3_add(vec3_add(
                    vec3_mul(camera_forward, 1.0),
                    vec3_mul(camera_right, u)),
                    vec3_mul(camera_up_corrected, v))
            );
            
            Ray ray = {camera_pos, ray_dir};
            
            // Trace ray
            Vec3 color = trace_ray(ray, spheres, sphere_count, light, MAX_DEPTH);
            
            // Convert to ASCII
            screen[HEIGHT - 1 - y][x] = color_to_ascii(color);
        }
    }
    
    // Display result
    printf("\n=== Ray Traced Scene ===\n");
    for (int y = 0; y < HEIGHT; y++) {
        for (int x = 0; x < WIDTH; x++) {
            printf("%c", screen[y][x]);
        }
        printf("\n");
    }
}

// Render with colors (ANSI escape codes)
void render_scene_colored(Sphere* spheres, int sphere_count, Light light) {
    printf("\n=== Ray Traced Scene (Colored) ===\n");
    
    // Camera setup
    Vec3 camera_pos = vec3(0, 0, -5);
    Vec3 camera_target = vec3(0, 0, 0);
    Vec3 camera_up = vec3(0, 1, 0);
    
    Vec3 camera_forward = vec3_normalize(vec3_sub(camera_target, camera_pos));
    Vec3 camera_right = vec3_normalize(vec3_cross(camera_forward, camera_up));
    Vec3 camera_up_corrected = vec3_cross(camera_right, camera_forward);
    
    double fov = 60.0 * M_PI / 180.0;
    double aspect_ratio = (double)WIDTH / HEIGHT;
    double half_height = tan(fov / 2.0);
    double half_width = aspect_ratio * half_height;
    
    for (int y = 0; y < HEIGHT; y++) {
        for (int x = 0; x < WIDTH; x++) {
            double u = (2.0 * x / WIDTH - 1.0) * half_width;
            double v = (1.0 - 2.0 * y / HEIGHT) * half_height;
            
            Vec3 ray_dir = vec3_normalize(
                vec3_add(vec3_add(
                    vec3_mul(camera_forward, 1.0),
                    vec3_mul(camera_right, u)),
                    vec3_mul(camera_up_corrected, v))
            );
            
            Ray ray = {camera_pos, ray_dir};
            Vec3 color = trace_ray(ray, spheres, sphere_count, light, MAX_DEPTH);
            
            // Map color to ANSI color
            int color_code = 0;
            if (color.x > 0.5) color_code += 1;  // Red
            if (color.y > 0.5) color_code += 2;  // Green
            if (color.z > 0.5) color_code += 4;  // Blue
            
            if (color_code == 0) {
                printf("\033[0;30m%c\033[0m", color_to_ascii(color)); // Black
            } else {
                printf("\033[1;3%dm%c\033[0m", color_code, color_to_ascii(color));
            }
        }
        printf("\n");
    }
}

// Create test scene
void create_test_scene(Sphere** spheres, int* sphere_count, Light* light) {
    *sphere_count = 5;
    *spheres = (Sphere*)malloc(*sphere_count * sizeof(Sphere));
    
    // Create spheres
    (*spheres)[0] = (Sphere){vec3(0, 0, 0), 1.0, 1};      // Red sphere
    (*spheres)[1] = (Sphere){vec3(-2, 0, 1), 0.8, 2};     // Green sphere
    (*spheres)[2] = (Sphere){vec3(2, 0, 1), 0.8, 3};      // Blue sphere
    (*spheres)[3] = (Sphere){vec3(0, -101, 0), 100, 4};   // Ground (large sphere)
    (*spheres)[4] = (Sphere){vec3(0, 2, 0), 0.5, 5};      // Top sphere (yellow)
    
    // Create light
    *light = (Light){vec3(5, 5, -5), vec3(1, 1, 1), 1.0};
}

// Animation function
void animate_scene() {
    Sphere* spheres;
    int sphere_count;
    Light light;
    
    create_test_scene(&spheres, &sphere_count, &light);
    
    printf("=== Ray Tracing Animation ===\n");
    printf("Press Enter to start animation (or Ctrl+C to exit)...\n");
    getchar();
    
    for (int frame = 0; frame < 20; frame++) {
        // Clear screen
        printf("\033[2J\033[H");
        
        // Animate light position
        double angle = frame * 0.3;
        light.position.x = 5.0 * cos(angle);
        light.position.z = 5.0 * sin(angle);
        
        // Animate spheres
        spheres[1].center.x = -2.0 + sin(angle) * 0.5;
        spheres[2].center.x = 2.0 + cos(angle) * 0.5;
        
        // Render frame
        render_scene(spheres, sphere_count, light);
        
        printf("Frame %d/20\n", frame + 1);
        
        // Small delay
        usleep(100000); // 0.1 second
    }
    
    free(spheres);
}

// Interactive scene editor
void interactive_scene() {
    Sphere* spheres;
    int sphere_count;
    Light light;
    
    create_test_scene(&spheres, &sphere_count, &light);
    
    printf("=== Interactive Ray Tracing ===\n");
    printf("Commands:\n");
    printf("  l <x> <y> <z> - Set light position\n");
    printf("  s <index> <x> <y> <z> - Set sphere position\n");
    printf("  r <index> <radius> - Set sphere radius\n");
    printf("  render - Render scene\n");
    printf("  color - Render colored scene\n");
    printf("  quit - Exit\n");
    
    char command[100];
    while (1) {
        printf("\n> ");
        fgets(command, sizeof(command), stdin);
        
        char cmd[20];
        sscanf(command, "%s", cmd);
        
        if (strcmp(cmd, "l") == 0) {
            double x, y, z;
            if (sscanf(command, "%*s %lf %lf %lf", &x, &y, &z) == 3) {
                light.position = vec3(x, y, z);
                printf("Light position set to (%.1f, %.1f, %.1f)\n", x, y, z);
            }
        } else if (strcmp(cmd, "s") == 0) {
            int index;
            double x, y, z;
            if (sscanf(command, "%*s %d %lf %lf %lf", &index, &x, &y, &z) == 4) {
                if (index >= 0 && index < sphere_count) {
                    spheres[index].center = vec3(x, y, z);
                    printf("Sphere %d position set to (%.1f, %.1f, %.1f)\n", index, x, y, z);
                }
            }
        } else if (strcmp(cmd, "r") == 0) {
            int index;
            double radius;
            if (sscanf(command, "%*s %d %lf", &index, &radius) == 2) {
                if (index >= 0 && index < sphere_count) {
                    spheres[index].radius = radius;
                    printf("Sphere %d radius set to %.1f\n", index, radius);
                }
            }
        } else if (strcmp(cmd, "render") == 0) {
            render_scene(spheres, sphere_count, light);
        } else if (strcmp(cmd, "color") == 0) {
            render_scene_colored(spheres, sphere_count, light);
        } else if (strcmp(cmd, "quit") == 0) {
            break;
        } else {
            printf("Unknown command\n");
        }
    }
    
    free(spheres);
}

int main() {
    printf("=== Ray Tracing Demo ===\n");
    
    Sphere* spheres;
    int sphere_count;
    Light light;
    
    create_test_scene(&spheres, &sphere_count, &light);
    
    // Render basic scene
    render_scene(spheres, sphere_count, light);
    
    // Render colored scene
    render_scene_colored(spheres, sphere_count, light);
    
    // Interactive mode
    printf("\nPress Enter for interactive mode, or Ctrl+C to exit...\n");
    getchar();
    
    interactive_scene();
    
    // Animation
    printf("\nPress Enter for animation, or Ctrl+C to exit...\n");
    getchar();
    
    animate_scene();
    
    free(spheres);
    
    printf("\n=== Ray tracing demo completed ===\n");
    
    return 0;
}
