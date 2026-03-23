# Generative Art System with Ruby
# Creates beautiful algorithmic art using mathematical patterns and creative coding

require 'gosu'
require 'colorize'
require 'json'

class GenerativeArtSystem
  def initialize(width = 800, height = 600)
    @width = width
    @height = height
    @canvas = Array.new(height) { Array.new(width) { [0, 0, 0] } }
    @time = 0
    @particles = []
    @fractals = []
    @waves = []
    @color_palettes = load_color_palettes
    @current_palette = @color_palettes[:sunset]
    @art_mode = :fractal
    @mouse_x = width / 2
    @mouse_y = height / 2
    @audio_reactive = false
    @frequency_data = Array.new(32) { 0 }
  end
  
  def start_interactive_mode
    puts "🎨 Starting Interactive Generative Art System"
    puts "Controls:"
    puts "  1-5: Switch art modes"
    puts "  Space: Pause/Resume"
    puts "  C: Change color palette"
    puts "  S: Save artwork"
    puts "  R: Reset canvas"
    puts "  A: Toggle audio reactive mode"
    puts "  Mouse: Interactive effects"
    puts "  ESC: Exit"
    
    # Start the interactive window (simplified console version)
    run_console_art
  end
  
  def create_fractal_art(type = :mandelbrot, iterations = 100)
    puts "🌀 Generating #{type.to_s.capitalize} Fractal..."
    
    case type
    when :mandelbrot
      generate_mandelbrot(iterations)
    when :julia
      generate_julia(iterations)
    when :burning_ship
      generate_burning_ship(iterations)
    when :sierpinski
      generate_sierpinski(iterations)
    when :dragon
      generate_dragon_curve(iterations)
    end
    
    save_artwork("fractal_#{type}_#{Time.now.to_i}")
    puts "✨ Fractal artwork saved!"
  end
  
  def create_particle_system(num_particles = 500)
    puts "✨ Creating Particle System with #{num_particles} particles..."
    
    initialize_particles(num_particles)
    
    1000.times do |frame|
      update_particles
      render_particles
      break if frame % 100 == 0 && frame > 0
    end
    
    save_artwork("particles_#{Time.now.to_i}")
    puts "🎆 Particle system artwork saved!"
  end
  
  def create_wave_art(wave_type = :sine, complexity = 5)
    puts "🌊 Creating #{wave_type.to_s.capitalize} Wave Art..."
    
    initialize_waves(complexity)
    
    @height.times do |y|
      @width.times do |x|
        color = calculate_wave_color(x, y, wave_type)
        @canvas[y][x] = color
      end
    end
    
    save_artwork("waves_#{wave_type}_#{Time.now.to_i}")
    puts "🌊 Wave artwork saved!"
  end
  
  def create_procedural_city(seed = nil)
    puts "🏙️ Generating Procedural City..."
    
    srand(seed || Time.now.to_i)
    
    # Generate city layout
    city_grid = generate_city_layout
    render_city(city_grid)
    
    save_artwork("procedural_city_#{Time.now.to_i}")
    puts "🌃 Procedural city saved!"
  end
  
  def create_generative_music_visualization(audio_file = nil)
    puts "🎵 Creating Music Visualization..."
    
    if audio_file
      analyze_audio(audio_file)
    else
      generate_synthetic_audio
    end
    
    render_audio_visualization
    save_artwork("music_viz_#{Time.now.to_i}")
    puts "🎶 Music visualization saved!"
  end
  
  def create_kaleidoscope_pattern(layers = 6)
    puts "🔮 Creating Kaleidoscope Pattern..."
    
    center_x = @width / 2
    center_y = @height / 2
    
    layers.times do |layer|
      radius = (@width / 2) * (layer + 1) / layers
      
      360.times do |angle|
        x = center_x + radius * Math.cos(angle * Math::PI / 180)
        y = center_y + radius * Math.sin(angle * Math::PI / 180)
        
        # Create symmetrical patterns
        (0...layers).each do |symmetry|
          sym_angle = angle + (360 * symmetry / layers)
          sym_x = center_x + radius * Math.cos(sym_angle * Math::PI / 180)
          sym_y = center_y + radius * Math.sin(sym_angle * Math::PI / 180)
          
          color = get_kaleidoscope_color(layer, angle)
          set_pixel(sym_x.round, sym_y.round, color)
        end
      end
    end
    
    save_artwork("kaleidoscope_#{Time.now.to_i}")
    puts "🔮 Kaleidoscope pattern saved!"
  end
  
  def create_cellular_automaton(ruleset = :game_of_life, generations = 100)
    puts "🦠 Creating Cellular Automaton: #{ruleset}"
    
    grid = initialize_cellular_grid(ruleset)
    
    generations.times do |generation|
      grid = evolve_cellular_grid(grid, ruleset)
      render_cellular_grid(grid, generation)
    end
    
    save_artwork("cellular_#{ruleset}_#{Time.now.to_i}")
    puts "🦠 Cellular automaton saved!"
  end
  
  def create_l_system_art(axiom = "F", rules = nil, iterations = 5)
    puts "🌿 Creating L-System Art..."
    
    rules ||= {
      'F' => 'FF+[+F-F-F]-[-F+F+F]',
      'X' => 'F+[[X]-X]-F[-FX]+X',
      'Y' => 'Y+FX[-Y-F+]+[+FY-Y]'
    }
    
    l_system_string = generate_l_system_string(axiom, rules, iterations)
    render_l_system(l_system_string)
    
    save_artwork("lsystem_#{Time.now.to_i}")
    puts "🌿 L-System art saved!"
  end
  
  def create_flow_field(flow_type = :perlin, density = 50)
    puts "🌊 Creating Flow Field Visualization..."
    
    flow_field = generate_flow_field(flow_type, density)
    render_flow_field(flow_field)
    
    save_artwork("flow_field_#{flow_type}_#{Time.now.to_i}")
    puts "🌊 Flow field saved!"
  end
  
  def create_voronoi_art(num_points = 50)
    puts "💎 Creating Voronoi Diagram Art..."
    
    points = generate_voronoi_points(num_points)
    voronoi_cells = calculate_voronoi_cells(points)
    render_voronoi(voronoi_cells)
    
    save_artwork("voronoi_#{Time.now.to_i}")
    puts "💎 Voronoi art saved!"
  end
  
  private
  
  def load_color_palettes
    {
      sunset: [
        [255, 94, 77],   # Coral
        [255, 154, 0],  # Orange
        [237, 117, 57], # Light Orange
        [255, 206, 84], # Yellow
        [255, 157, 77], # Peach
        [237, 85, 59],  # Red Orange
        [255, 111, 97], # Salmon
        [255, 195, 113] # Light Yellow
      ],
      ocean: [
        [0, 119, 190],   # Ocean Blue
        [0, 180, 216],   # Sky Blue
        [144, 224, 239], # Light Blue
        [72, 202, 228],  # Turquoise
        [0, 150, 199],   # Deep Blue
        [3, 4, 94],      # Navy
        [175, 238, 238], # Pale Turquoise
        [64, 224, 208]  # Turquoise Green
      ],
      forest: [
        [34, 139, 34],   # Forest Green
        [107, 142, 35], # Olive Green
        [124, 252, 0],  # Lawn Green
        [0, 100, 0],    # Dark Green
        [85, 107, 47],  # Dark Olive Green
        [143, 188, 143], # Light Green
        [46, 125, 50],  # Pine Green
        [154, 205, 50]  # Yellow Green
      ],
      cosmic: [
        [75, 0, 130],    # Indigo
        [138, 43, 226],  # Blue Violet
        [255, 0, 255],   # Magenta
        [148, 0, 211],   # Dark Violet
        [153, 50, 204],  # Dark Orchid
        [186, 85, 211],  # Medium Orchid
        [218, 112, 214], # Orchid
        [238, 130, 238]  # Violet
      ],
      monochrome: [
        [0, 0, 0],       # Black
        [64, 64, 64],    # Dark Gray
        [128, 128, 128], # Gray
        [192, 192, 192], # Light Gray
        [255, 255, 255]  # White
      ]
    }
  end
  
  def generate_mandelbrot(max_iterations)
    @height.times do |y|
      @width.times do |x|
        zx = 0
        zy = 0
        cx = (x - @width / 2) * 4.0 / @width
        cy = (y - @height / 2) * 4.0 / @height
        
        iteration = 0
        
        while zx * zx + zy * zy < 4 && iteration < max_iterations
          tmp = zx * zx - zy * zy + cx
          zy = 2 * zx * zy + cy
          zx = tmp
          iteration += 1
        end
        
        color = get_fractal_color(iteration, max_iterations)
        @canvas[y][x] = color
      end
    end
  end
  
  def generate_julia(max_iterations)
    @height.times do |y|
      @width.times do |x|
        zx = (x - @width / 2) * 4.0 / @width
        zy = (y - @height / 2) * 4.0 / @height
        cx = -0.7
        cy = 0.27015
        
        iteration = 0
        
        while zx * zx + zy * zy < 4 && iteration < max_iterations
          tmp = zx * zx - zy * zy + cx
          zy = 2 * zx * zy + cy
          zx = tmp
          iteration += 1
        end
        
        color = get_fractal_color(iteration, max_iterations)
        @canvas[y][x] = color
      end
    end
  end
  
  def generate_burning_ship(max_iterations)
    @height.times do |y|
      @width.times do |x|
        zx = 0
        zy = 0
        cx = (x - @width / 2) * 4.0 / @width
        cy = (y - @height / 2) * 4.0 / @height
        
        iteration = 0
        
        while zx * zx + zy * zy < 4 && iteration < max_iterations
          tmp = zx * zx - zy * zy + cx
          zy = 2 * zx.abs * zy.abs + cy
          zx = tmp
          iteration += 1
        end
        
        color = get_fractal_color(iteration, max_iterations)
        @canvas[y][x] = color
      end
    end
  end
  
  def generate_sierpinski(iterations)
    # Start with a triangle
    points = [
      [@width / 2, 50],
      [50, @height - 50],
      [@width - 50, @height - 50]
    ]
    
    iterations.times do
      new_points = []
      
      points.each_with_index do |point, i|
        next_point = points[(i + 1) % points.length]
        
        # Calculate midpoint
        mid_x = (point[0] + next_point[0]) / 2
        mid_y = (point[1] + next_point[1]) / 2
        
        new_points << point
        new_points << [mid_x, mid_y]
      end
      
      points = new_points
    end
    
    # Draw the fractal
    points.each_with_index do |point, i|
      next_point = points[(i + 1) % points.length]
      draw_line(point[0], point[1], next_point[0], next_point[1])
    end
  end
  
  def generate_dragon_curve(iterations)
    points = [[@width / 3, @height / 2], [2 * @width / 3, @height / 2]]
    
    iterations.times do
      new_points = [points.first]
      
      points.each_with_index do |point, i|
        next unless i > 0
        
        prev_point = points[i - 1]
        
        # Calculate dragon curve transformation
        dx = point[0] - prev_point[0]
        dy = point[1] - prev_point[1]
        
        # Rotate 90 degrees and scale
        new_x = prev_point[0] - dy / 2
        new_y = prev_point[1] + dx / 2
        
        new_points << [new_x, new_y]
        new_points << point
      end
      
      points = new_points
    end
    
    # Draw the dragon curve
    points.each_with_index do |point, i|
      next unless i > 0
      
      prev_point = points[i - 1]
      draw_line(prev_point[0], prev_point[1], point[0], point[1])
    end
  end
  
  def get_fractal_color(iteration, max_iterations)
    if iteration == max_iterations
      [0, 0, 0]  # Black for points in the set
    else
      # Color based on iteration count
      hue = (iteration.to_f / max_iterations) * 360
      rgb = hsl_to_rgb(hue, 0.8, 0.5)
      rgb
    end
  end
  
  def initialize_particles(num_particles)
    @particles = []
    
    num_particles.times do |i|
      @particles << {
        x: rand(@width),
        y: rand(@height),
        vx: rand(-2..2),
        vy: rand(-2..2),
        size: rand(1..5),
        color: @current_palette.sample,
        life: 100,
        max_life: 100,
        type: [:circle, :square, :triangle].sample
      }
    end
  end
  
  def update_particles
    @particles.each do |particle|
      # Update position
      particle[:x] += particle[:vx]
      particle[:y] += particle[:vy]
      
      # Apply gravity
      particle[:vy] += 0.1
      
      # Apply mouse attraction
      dx = @mouse_x - particle[:x]
      dy = @mouse_y - particle[:y]
      dist = Math.sqrt(dx * dx + dy * dy)
      
      if dist > 0 && dist < 200
        force = 50 / dist
        particle[:vx] += dx / dist * force * 0.01
        particle[:vy] += dy / dist * force * 0.01
      end
      
      # Bounce off walls
      if particle[:x] <= 0 || particle[:x] >= @width
        particle[:vx] *= -0.8
        particle[:x] = [[0, particle[:x]].max, @width].min
      end
      
      if particle[:y] <= 0 || particle[:y] >= @height
        particle[:vy] *= -0.8
        particle[:y] = [[0, particle[:y]].max, @height].min
      end
      
      # Update life
      particle[:life] -= 1
      
      # Respawn dead particles
      if particle[:life] <= 0
        particle[:x] = rand(@width)
        particle[:y] = 0
        particle[:vx] = rand(-2..2)
        particle[:vy] = rand(0..2)
        particle[:life] = particle[:max_life]
        particle[:color] = @current_palette.sample
      end
    end
  end
  
  def render_particles
    @particles.each do |particle|
      alpha = particle[:life].to_f / particle[:max_life]
      color = particle[:color].map { |c| (c * alpha).round }
      
      draw_particle(particle[:x], particle[:y], particle[:size], color, particle[:type])
    end
  end
  
  def draw_particle(x, y, size, color, type)
    case type
    when :circle
      draw_circle(x, y, size, color)
    when :square
      draw_square(x - size/2, y - size/2, size, color)
    when :triangle
      draw_triangle(x, y, size, color)
    end
  end
  
  def draw_circle(x, y, radius, color)
    (y - radius..y + radius).each do |cy|
      (x - radius..x + radius).each do |cx|
        dx = cx - x
        dy = cy - y
        if dx * dx + dy * dy <= radius * radius
          set_pixel(cx, cy, color)
        end
      end
    end
  end
  
  def draw_square(x, y, size, color)
    (y..y + size).each do |cy|
      (x..x + size).each do |cx|
        set_pixel(cx, cy, color)
      end
    end
  end
  
  def draw_triangle(x, y, size, color)
    height = size * Math.sqrt(3) / 2
    
    # Draw triangle using lines
    y1 = y - height / 2
    y2 = y + height / 2
    x1 = x - size / 2
    x2 = x + size / 2
    
    draw_line(x, y1, x1, y2, color)
    draw_line(x, y1, x2, y2, color)
    draw_line(x1, y2, x2, y2, color)
  end
  
  def draw_line(x1, y1, x2, y2, color = [255, 255, 255])
    dx = (x2 - x1).abs
    dy = (y2 - y1).abs
    sx = x1 < x2 ? 1 : -1
    sy = y1 < y2 ? 1 : -1
    err = dx - dy
    
    x, y = x1, y1
    
    loop do
      set_pixel(x, y, color)
      
      break if x == x2 && y == y2
      
      e2 = 2 * err
      
      if e2 > -dy
        err -= dy
        x += sx
      end
      
      if e2 < dx
        err += dx
        y += sy
      end
    end
  end
  
  def initialize_waves(complexity)
    @waves = []
    
    complexity.times do |i|
      @waves << {
        amplitude: rand(20..100),
        frequency: rand(0.01..0.1),
        phase: rand(0..2 * Math::PI),
        speed: rand(0.01..0.05),
        color: @current_palette[i % @current_palette.length]
      }
    end
  end
  
  def calculate_wave_color(x, y, wave_type)
    value = 0
    
    @waves.each do |wave|
      case wave_type
      when :sine
        value += wave[:amplitude] * Math.sin(x * wave[:frequency] + @time * wave[:speed] + wave[:phase])
      when :cosine
        value += wave[:amplitude] * Math.cos(x * wave[:frequency] + @time * wave[:speed] + wave[:phase])
      when :radial
        dx = x - @width / 2
        dy = y - @height / 2
        dist = Math.sqrt(dx * dx + dy * dy)
        value += wave[:amplitude] * Math.sin(dist * wave[:frequency] + @time * wave[:speed])
      when :turbulent
        value += wave[:amplitude] * Math.sin(x * wave[:frequency] + @time * wave[:speed]) *
                 Math.cos(y * wave[:frequency] + @time * wave[:speed] + wave[:phase])
      end
    end
    
    # Normalize value to 0-255 range
    normalized = ((value + @waves.sum { |w| w[:amplitude] }) / (2 * @waves.sum { |w| w[:amplitude] }) * 255).round
    
    # Apply color based on wave that contributed most
    dominant_wave = @waves.max_by { |w| w[:amplitude] }
    intensity = normalized / 255.0
    
    dominant_wave[:color].map { |c| (c * intensity).round }
  end
  
  def generate_city_layout
    city = Array.new(20) { Array.new(20) { :empty } }
    
    # Generate main roads
    5.times do |i|
      x = rand(20)
      y = rand(20)
      
      # Create cross roads
      city[y].fill(:road, x, 20 - x)
      20.times { |cy| city[cy][x] = :road }
    end
    
    # Generate buildings
    100.times do
      x = rand(20)
      y = rand(20)
      
      if city[y][x] == :empty
        building_type = [:residential, :commercial, :industrial].sample
        city[y][x] = building_type
      end
    end
    
    city
  end
  
  def render_city(city_grid)
    cell_width = @width / 20
    cell_height = @height / 20
    
    city_grid.each_with_index do |row, y|
      row.each_with_index do |cell, x|
        cell_x = x * cell_width
        cell_y = y * cell_height
        
        color = case cell
                when :empty
                  [50, 50, 50]  # Dark gray for empty lots
                when :road
                  [80, 80, 80]  # Gray for roads
                when :residential
                  [100, 150, 100]  # Light green
                when :commercial
                  [150, 100, 150]  # Purple
                when :industrial
                  [150, 150, 100]  # Brown
                end
        
        # Draw cell
        (cell_y..cell_y + cell_height).each do |cy|
          (cell_x..cell_x + cell_width).each do |cx|
            set_pixel(cx, cy, color)
          end
        end
        
        # Add buildings
        if [:residential, :commercial, :industrial].include?(cell)
          building_height = rand(3..10)
          building_color = color.map { |c| [c + 50, 255].min }
          
          (cell_y..cell_y + cell_height - 5).each do |cy|
            (cell_x + 2..cell_x + cell_width - 2).each do |cx|
              set_pixel(cx, cy, building_color)
            end
          end
        end
      end
    end
  end
  
  def analyze_audio(audio_file)
    # Simulate audio analysis
    @frequency_data = Array.new(32) { rand(255) }
  end
  
  def generate_synthetic_audio
    # Generate synthetic frequency data
    @time = 0
    
    32.times do |i|
      frequency = i * 100
      @frequency_data[i] = (Math.sin(frequency * @time * 0.001) * 127 + 128).round
    end
  end
  
  def render_audio_visualization
    bar_width = @width / 32
    
    @frequency_data.each_with_index do |frequency, i|
      bar_height = (frequency / 255.0) * @height
      x = i * bar_width
      
      # Draw frequency bar
      (0..bar_height).each do |y|
        color = @current_palette[i % @current_palette.length]
        intensity = y / bar_height
        pixel_color = color.map { |c| (c * intensity).round }
        
        (x..x + bar_width - 1).each do |cx|
          set_pixel(cx, @height - y - 1, pixel_color)
        end
      end
    end
  end
  
  def get_kaleidoscope_color(layer, angle)
    color_index = (layer + angle / 60) % @current_palette.length
    @current_palette[color_index]
  end
  
  def initialize_cellular_grid(ruleset)
    case ruleset
    when :game_of_life
      Array.new(@height) { Array.new(@width) { rand < 0.3 } }
    when :elementary
      Array.new(@height) { Array.new(@width) { rand < 0.5 } }
    when :totalistic
      Array.new(@height) { Array.new(@width) { rand(3) } }
    end
  end
  
  def evolve_cellular_grid(grid, ruleset)
    new_grid = Array.new(@height) { Array.new(@width) }
    
    (1..@height - 2).each do |y|
      (1..@width - 2).each do |x|
        neighbors = count_neighbors(grid, x, y)
        
        case ruleset
        when :game_of_life
          if grid[y][x]
            new_grid[y][x] = neighbors == 2 || neighbors == 3
          else
            new_grid[y][x] = neighbors == 3
          end
        when :elementary
          rule = 30  # Rule 30 elementary cellular automaton
          state = (neighbors << 1) | (grid[y][x] ? 1 : 0)
          new_grid[y][x] = ((rule >> state) & 1) == 1
        when :totalistic
          new_grid[y][x] = neighbors % 3
        end
      end
    end
    
    new_grid
  end
  
  def count_neighbors(grid, x, y)
    count = 0
    
    (-1..1).each do |dy|
      (-1..1).each do |dx|
        next if dx == 0 && dy == 0
        
        nx = x + dx
        ny = y + dy
        
        if nx >= 0 && nx < @width && ny >= 0 && ny < @height
          count += 1 if grid[ny][nx]
        end
      end
    end
    
    count
  end
  
  def render_cellular_grid(grid, generation)
    grid.each_with_index do |row, y|
      row.each_with_index do |cell, x|
        if cell
          color = @current_palette[generation % @current_palette.length]
          set_pixel(x, y, color)
        else
          set_pixel(x, y, [0, 0, 0])
        end
      end
    end
  end
  
  def generate_l_system_string(axiom, rules, iterations)
    current = axiom
    
    iterations.times do
      next_string = ""
      
      current.each_char do |char|
        next_string += rules[char] || char
      end
      
      current = next_string
    end
    
    current
  end
  
  def render_l_system(l_system_string)
    x = @width / 2
    y = @height - 50
    angle = 0
    length = 5
    stack = []
    
    l_system_string.each_char do |char|
      case char
      when 'F'
        new_x = x + length * Math.cos(angle * Math::PI / 180)
        new_y = y + length * Math.sin(angle * Math::PI / 180)
        
        draw_line(x, y, new_x, new_y, @current_palette.sample)
        
        x = new_x
        y = new_y
      when '+'
        angle += 25
      when '-'
        angle -= 25
      when '['
        stack << [x, y, angle]
      when ']'
        x, y, angle = stack.pop if stack.any?
      end
    end
  end
  
  def generate_flow_field(flow_type, density)
    field = Array.new(density) { Array.new(density) }
    
    density.times do |y|
      density.times do |x|
        case flow_type
        when :perlin
          # Simplified Perlin noise
          field[y][x] = Math.sin(x * 0.1) * Math.cos(y * 0.1)
        when :vortex
          dx = x - density / 2
          dy = y - density / 2
          field[y][x] = Math.atan2(dy, dx)
        when :spiral
          angle = Math.sqrt(x * x + y * y) * 0.1
          field[y][x] = angle
        end
      end
    end
    
    field
  end
  
  def render_flow_field(flow_field)
    cell_width = @width / flow_field.length
    cell_height = @height / flow_field.first.length
    
    flow_field.each_with_index do |row, y|
      row.each_with_index do |angle, x|
        cx = x * cell_width + cell_width / 2
        cy = y * cell_height + cell_height / 2
        
        # Draw flow vector
        length = cell_width / 2
        end_x = cx + length * Math.cos(angle)
        end_y = cy + length * Math.sin(angle)
        
        draw_line(cx, cy, end_x, end_y, @current_palette.sample)
      end
    end
  end
  
  def generate_voronoi_points(num_points)
    points = []
    
    num_points.times do
      points << {
        x: rand(@width),
        y: rand(@height),
        color: @current_palette.sample
      }
    end
    
    points
  end
  
  def calculate_voronoi_cells(points)
    cells = Array.new(@height) { Array.new(@width) }
    
    @height.times do |y|
      @width.times do |x|
        min_dist = Float::INFINITY
        closest_point = nil
        
        points.each do |point|
          dist = Math.sqrt((x - point[:x]) ** 2 + (y - point[:y]) ** 2)
          
          if dist < min_dist
            min_dist = dist
            closest_point = point
          end
        end
        
        cells[y][x] = closest_point[:color]
      end
    end
    
    cells
  end
  
  def render_voronoi(cells)
    cells.each_with_index do |row, y|
      row.each_with_index do |color, x|
        set_pixel(x, y, color)
      end
    end
  end
  
  def set_pixel(x, y, color)
    return unless x >= 0 && x < @width && y >= 0 && y < @height
    
    @canvas[y][x] = color
  end
  
  def hsl_to_rgb(h, s, l)
    h = h / 360.0
    
    if s == 0
      r = g = b = l
    else
      q = l < 0.5 ? l * (1 + s) : l + s - l * s
      p = 2 * l - q
      
      r = hue_to_rgb(p, q, h + 1/3.0)
      g = hue_to_rgb(p, q, h)
      b = hue_to_rgb(p, q, h - 1/3.0)
    end
    
    [(r * 255).round, (g * 255).round, (b * 255).round]
  end
  
  def hue_to_rgb(p, q, t)
    t -= 1 if t < 0
    t += 1 if t > 1
    
    return p + (q - p) * 6 * t if t < 1/6.0
    return q if t < 1/2.0
    return p + (q - p) * (2/3.0 - t) * 6 if t < 2/3.0
    
    p
  end
  
  def save_artwork(filename)
    # Save as ASCII art for console display
    ascii_art = canvas_to_ascii
    
    File.write("artwork_#{filename}.txt", ascii_art)
    
    # Also save as JSON for potential reconstruction
    File.write("artwork_#{filename}.json", JSON.generate(@canvas))
  end
  
  def canvas_to_ascii
    ascii = ""
    
    @height.times do |y|
      @width.times do |x|
        r, g, b = @canvas[y][x]
        
        # Convert RGB to grayscale
        gray = (r * 0.299 + g * 0.587 + b * 0.114).round
        
        # Map to ASCII characters
        char = case gray
               when 0..63
                 ' '
               when 64..127
                 '░'
               when 128..191
                 '▒'
               when 192..255
                 '█'
               end
        
        ascii += char
      end
      
      ascii += "\n"
    end
    
    ascii
  end
  
  def run_console_art
    puts "🎨 Console Art Mode"
    puts "Press Enter to generate new art, 'q' to quit"
    
    loop do
      input = gets.chomp.downcase
      
      case input
      when 'q'
        break
      when ''
        # Generate random art
        art_types = [:fractal, :particles, :waves, :kaleidoscope, :cellular]
        art_type = art_types.sample
        
        case art_type
        when :fractal
          fractal_types = [:mandelbrot, :julia, :burning_ship, :sierpinski, :dragon]
          create_fractal_art(fractal_types.sample, 50)
        when :particles
          create_particle_system(100)
        when :waves
          wave_types = [:sine, :cosine, :radial, :turbulent]
          create_wave_art(wave_types.sample, 3)
        when :kaleidoscope
          create_kaleidoscope_pattern(4)
        when :cellular
          cellular_types = [:game_of_life, :elementary, :totalistic]
          create_cellular_automaton(cellular_types.sample, 50)
        end
        
        puts "✨ New #{art_type} artwork generated!"
      end
    end
  end
end

# Interactive Art Generator
class InteractiveArtGenerator
  def initialize
    @art_system = GenerativeArtSystem.new
    @running = true
  end
  
  def start
    puts "🎨 Welcome to the Interactive Generative Art System!"
    puts "\nAvailable Art Types:"
    puts "1. Fractal Art"
    puts "2. Particle Systems"
    puts "3. Wave Art"
    puts "4. Procedural City"
    puts "5. Music Visualization"
    puts "6. Kaleidoscope"
    puts "7. Cellular Automaton"
    puts "8. L-System"
    puts "9. Flow Field"
    puts "10. Voronoi Diagram"
    puts "11. Random Mix"
    puts "12. Interactive Mode"
    
    while @running
      print "\nChoose an art type (1-12): "
      choice = gets.chomp.to_i
      
      case choice
      when 1
        create_fractal_menu
      when 2
        create_particle_menu
      when 3
        create_wave_menu
      when 4
        @art_system.create_procedural_city
      when 5
        @art_system.create_generative_music_visualization
      when 6
        create_kaleidoscope_menu
      when 7
        create_cellular_menu
      when 8
        create_lsystem_menu
      when 9
        create_flow_field_menu
      when 10
        create_voronoi_menu
      when 11
        create_random_mix
      when 12
        @art_system.start_interactive_mode
      when 0
        @running = false
      else
        puts "Invalid choice. Please try again."
      end
    end
    
    puts "🎨 Thank you for using the Generative Art System!"
  end
  
  private
  
  def create_fractal_menu
    puts "\nFractal Types:"
    puts "1. Mandelbrot Set"
    puts "2. Julia Set"
    puts "3. Burning Ship"
    puts "4. Sierpinski Triangle"
    puts "5. Dragon Curve"
    
    print "Choose fractal type (1-5): "
    choice = gets.chomp.to_i
    
    fractal_types = [:mandelbrot, :julia, :burning_ship, :sierpinski, :dragon]
    fractal_type = fractal_types[choice - 1] || :mandelbrot
    
    print "Number of iterations (10-500): "
    iterations = gets.chomp.to_i
    iterations = 100 if iterations < 10 || iterations > 500
    
    @art_system.create_fractal_art(fractal_type, iterations)
  end
  
  def create_particle_menu
    print "Number of particles (10-1000): "
    num_particles = gets.chomp.to_i
    num_particles = 500 if num_particles < 10 || num_particles > 1000
    
    @art_system.create_particle_system(num_particles)
  end
  
  def create_wave_menu
    puts "\nWave Types:"
    puts "1. Sine Waves"
    puts "2. Cosine Waves"
    puts "3. Radial Waves"
    puts "4. Turbulent Waves"
    
    print "Choose wave type (1-4): "
    choice = gets.chomp.to_i
    
    wave_types = [:sine, :cosine, :radial, :turbulent]
    wave_type = wave_types[choice - 1] || :sine
    
    print "Complexity (1-10): "
    complexity = gets.chomp.to_i
    complexity = 5 if complexity < 1 || complexity > 10
    
    @art_system.create_wave_art(wave_type, complexity)
  end
  
  def create_kaleidoscope_menu
    print "Number of layers (3-12): "
    layers = gets.chomp.to_i
    layers = 6 if layers < 3 || layers > 12
    
    @art_system.create_kaleidoscope_pattern(layers)
  end
  
  def create_cellular_menu
    puts "\nCellular Automaton Types:"
    puts "1. Conway's Game of Life"
    puts "2. Elementary Cellular Automaton"
    puts "3. Totalistic Cellular Automaton"
    
    print "Choose type (1-3): "
    choice = gets.chomp.to_i
    
    cellular_types = [:game_of_life, :elementary, :totalistic]
    cellular_type = cellular_types[choice - 1] || :game_of_life
    
    print "Number of generations (10-500): "
    generations = gets.chomp.to_i
    generations = 100 if generations < 10 || generations > 500
    
    @art_system.create_cellular_automaton(cellular_type, generations)
  end
  
  def create_lsystem_menu
    print "Iterations (1-10): "
    iterations = gets.chomp.to_i
    iterations = 5 if iterations < 1 || iterations > 10
    
    @art_system.create_l_system_art("F", nil, iterations)
  end
  
  def create_flow_field_menu
    puts "\nFlow Field Types:"
    puts "1. Perlin Noise"
    puts "2. Vortex"
    puts "3. Spiral"
    
    print "Choose type (1-3): "
    choice = gets.chomp.to_i
    
    flow_types = [:perlin, :vortex, :spiral]
    flow_type = flow_types[choice - 1] || :perlin
    
    print "Field density (10-100): "
    density = gets.chomp.to_i
    density = 50 if density < 10 || density > 100
    
    @art_system.create_flow_field(flow_type, density)
  end
  
  def create_voronoi_menu
    print "Number of points (5-100): "
    num_points = gets.chomp.to_i
    num_points = 50 if num_points < 5 || num_points > 100
    
    @art_system.create_voronoi_art(num_points)
  end
  
  def create_random_mix
    puts "🎲 Creating Random Art Mix..."
    
    # Create multiple art pieces
    @art_system.create_fractal_art(:mandelbrot, 50)
    @art_system.create_particle_system(200)
    @art_system.create_wave_art(:sine, 3)
    @art_system.create_kaleidoscope_pattern(4)
    
    puts "✨ Random art mix created!"
  end
end

# Main execution
if __FILE__ == $0
  puts "🎨 Generative Art System"
  puts "====================="
  
  generator = InteractiveArtGenerator.new
  generator.start
end
