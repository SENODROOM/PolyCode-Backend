# Events - Event classes and definitions

class Event
  attr_reader :type, :data, :timestamp
  
  def initialize(type, data = {})
    @type = type
    @data = data
    @timestamp = Time.now
  end
  
  def to_s
    "#{@type}: #{@data}"
  end
end

class EntityEvent < Event
  attr_reader :entity_id
  
  def initialize(type, entity_id, data = {})
    super(type, data.merge(entity_id: entity_id))
    @entity_id = entity_id
  end
end

class CollisionEvent < EntityEvent
  attr_reader :other_entity_id
  
  def initialize(entity_id, other_entity_id, data = {})
    super(:collision, entity_id, data.merge(other_entity_id: other_entity_id))
    @other_entity_id = other_entity_id
  end
  
  def to_s
    "Collision between #{@entity_id} and #{@other_entity_id}"
  end
end

class InputEvent < Event
  attr_reader :key, :action
  
  def initialize(key, action, data = {})
    super(:input_received, data.merge(key: key, action: action))
    @key = key
    @action = action
  end
  
  def to_s
    "Input #{@action}: #{@key}"
  end
end

class GameStateEvent < Event
  def initialize(state, data = {})
    super(state, data)
  end
  
  def to_s
    "Game state: #{@type}"
  end
end
