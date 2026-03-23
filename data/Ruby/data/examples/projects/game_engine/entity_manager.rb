# Entity Manager - Manages entities and components

class EntityManager
  def initialize
    @entities = {}
    @components = {}
    @next_entity_id = 1
  end
  
  def create_entity
    entity_id = @next_entity_id
    @entities[entity_id] = Set.new
    @next_entity_id += 1
    entity_id
  end
  
  def remove_entity(entity_id)
    @entities.delete(entity_id)
    
    # Remove all components for this entity
    @components.each do |component_type, entity_components|
      entity_components.delete(entity_id)
    end
  end
  
  def add_component(entity_id, component)
    component_type = component.class
    
    @entities[entity_id] ||= Set.new
    @entities[entity_id].add(component_type)
    
    @components[component_type] ||= {}
    @components[component_type][entity_id] = component
  end
  
  def remove_component(entity_id, component_type)
    if @entities[entity_id]
      @entities[entity_id].delete(component_type)
    end
    
    if @components[component_type]
      @components[component_type].delete(entity_id)
    end
  end
  
  def get_component(entity_id, component_type)
    components = @components[component_type]
    components ? components[entity_id] : nil
  end
  
  def has_component?(entity_id, component_type)
    components = @entities[entity_id]
    components ? components.include?(component_type) : false
  end
  
  def get_entities_with_components(component_types)
    return [] if component_types.empty?
    
    # Start with entities that have the first component type
    first_type = component_types.first
    entities_with_first = (@components[first_type] || {}).keys
    
    # Filter by remaining component types
    entities_with_first.select do |entity_id|
      component_types[1..-1].all? do |component_type|
        has_component?(entity_id, component_type)
      end
    end
  end
  
  def get_all_entities
    @entities.keys
  end
  
  def get_components_of_type(component_type)
    @components[component_type] || {}
  end
  
  def clear_all
    @entities.clear
    @components.clear
    @next_entity_id = 1
  end
  
  def entity_count
    @entities.length
  end
  
  def component_count
    @components.values.sum(&:length)
  end
end

# Simple Set implementation for compatibility
class Set
  def initialize
    @items = []
  end
  
  def add(item)
    @items << item unless @items.include?(item)
  end
  
  def include?(item)
    @items.include?(item)
  end
  
  def delete(item)
    @items.delete(item)
  end
  
  def clear
    @items.clear
  end
  
  def empty?
    @items.empty?
  end
  
  def each(&block)
    @items.each(&block)
  end
  
  def length
    @items.length
  end
  
  def to_a
    @items.dup
  end
end
