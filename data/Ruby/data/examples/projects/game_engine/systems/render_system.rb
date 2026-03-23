# Render System - Handles rendering of entities

class RenderSystem
  def initialize(renderer)
    @renderer = renderer
  end
  
  def update(entity_manager, delta_time)
    # The actual rendering is handled by the renderer
    # This system could handle animation updates, visibility changes, etc.
    
    entities = entity_manager.get_entities_with_components([Position, Render])
    
    entities.each do |entity|
      position = entity_manager.get_component(entity, Position)
      render = entity_manager.get_component(entity, Render)
      
      # Update render component based on position or other factors
      # For now, just ensure the entity is visible if it has a render component
      render.show if render && !render.visible?
    end
  end
end
