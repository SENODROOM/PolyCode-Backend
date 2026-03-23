# Input System - Handles user input for controllable entities

class InputSystem
  def initialize(input_handler)
    @input_handler = input_handler
    @move_speed = 1.0
    @jump_velocity = -8.0
  end
  
  def update(entity_manager, delta_time)
    entities = entity_manager.get_entities_with_components([Input, Position, Velocity])
    
    entities.each do |entity|
      input = entity_manager.get_component(entity, Input)
      velocity = entity_manager.get_component(entity, Velocity)
      
      next unless input && velocity && input.enabled?
      
      if input.player?
        handle_player_input(entity, entity_manager)
      elsif input.ai?
        handle_ai_input(entity, entity_manager)
      end
    end
  end
  
  private
  
  def handle_player_input(entity, entity_manager)
    velocity = entity_manager.get_component(entity, Velocity)
    input = entity_manager.get_component(entity, Input)
    
    # Handle movement
    if @input_handler.key_pressed?(:left)
      velocity.vx = -@move_speed
    elsif @input_handler.key_pressed?(:right)
      velocity.vx = @move_speed
    else
      velocity.vx = 0
    end
    
    if @input_handler.key_pressed?(:up)
      velocity.vy = -@move_speed
    elsif @input_handler.key_pressed?(:down)
      velocity.vy = @move_speed
    else
      velocity.vy = 0
    end
    
    # Handle jump
    if @input_handler.key_pressed?(:space)
      position = entity_manager.get_component(entity, Position)
      physics = entity_manager.get_component(entity, Physics)
      
      # Simple jump - only allow if on ground (y position is at bottom)
      if position && position.y >= 29
        velocity.vy = @jump_velocity
      end
    end
  end
  
  def handle_ai_input(entity, entity_manager)
    # Simple AI - could be expanded for more complex behavior
    velocity = entity_manager.get_component(entity, Velocity)
    position = entity_manager.get_component(entity, Position)
    
    # Random movement for demonstration
    if rand < 0.02  # 2% chance to change direction
      velocity.vx = rand(-1.0..1.0) * @move_speed
      velocity.vy = rand(-1.0..1.0) * @move_speed
    end
  end
end
