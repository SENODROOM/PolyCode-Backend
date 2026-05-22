# Movement System - Handles entity movement based on velocity

class MovementSystem
  def initialize
    @gravity = 0.5
    @jump_velocity = -8.0
    @move_speed = 2.0
  end
  
  def update(entity_manager, delta_time)
    entities = entity_manager.get_entities_with_components([Position, Velocity])
    
    entities.each do |entity|
      position = entity_manager.get_component(entity, Position)
      velocity = entity_manager.get_component(entity, Velocity)
      physics = entity_manager.get_component(entity, Physics)
      
      next unless position && velocity
      
      # Apply gravity if affected
      if physics && physics.gravity_affected?
        velocity.vy += @gravity * delta_time
      end
      
      # Update position based on velocity
      position.x += velocity.vx * delta_time * 60  # Scale for frame independence
      position.y += velocity.vy * delta_time * 60
      
      # Apply friction
      velocity.apply_friction(0.9)
      
      # Boundary checking
      position.x = [[position.x, 0].max, 50].min
      position.y = [[position.y, 0].max, 30].min
      
      # Stop at boundaries
      if position.x <= 0 || position.x >= 50
        velocity.vx = 0
      end
      
      if position.y <= 0 || position.y >= 30
        velocity.vy = 0
      end
    end
  end
end
