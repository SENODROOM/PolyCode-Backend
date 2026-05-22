# Physics System - Handles collision detection and physics

class PhysicsSystem
  def initialize
    @collision_handlers = []
  end
  
  def update(entity_manager, delta_time)
    entities = entity_manager.get_entities_with_components([Position, Physics])
    
    # Check collisions between solid entities
    solid_entities = entities.select do |entity|
      physics = entity_manager.get_component(entity, Physics)
      physics && physics.solid?
    end
    
    # Check for collisions
    solid_entities.each_with_index do |entity1, i|
      position1 = entity_manager.get_component(entity1, Position)
      
      solid_entities[(i+1)..-1].each do |entity2|
        position2 = entity_manager.get_component(entity2, Position)
        
        if check_collision(position1, position2)
          handle_collision(entity1, entity2, entity_manager)
        end
      end
    end
  end
  
  private
  
  def check_collision(pos1, pos2)
    # Simple AABB collision detection (1x1 entities)
    pos1.x == pos2.x && pos1.y == pos2.y
  end
  
  def handle_collision(entity1, entity2, entity_manager)
    # Get velocities if they exist
    vel1 = entity_manager.get_component(entity1, Velocity)
    vel2 = entity_manager.get_component(entity2, Velocity)
    
    # Simple collision response - stop both entities
    if vel1
      vel1.vx = 0
      vel1.vy = 0
    end
    
    if vel2
      vel2.vx = 0
      vel2.vy = 0
    end
    
    # Separate entities (simple separation)
    pos1 = entity_manager.get_component(entity1, Position)
    pos2 = entity_manager.get_component(entity2, Position)
    
    if pos1 && pos2
      # Simple separation - move entity2 away
      if pos2.x < pos1.x
        pos2.x -= 1
      elsif pos2.x > pos1.x
        pos2.x += 1
      elsif pos2.y < pos1.y
        pos2.y -= 1
      else
        pos2.y += 1
      end
    end
  end
end
