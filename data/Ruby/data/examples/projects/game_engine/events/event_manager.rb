# Event Manager - Handles event system for game engine

class EventManager
  def initialize
    @listeners = Hash.new { |h, k| h[k] = [] }
  end
  
  def subscribe(event_type, listener)
    @listeners[event_type] << listener
  end
  
  def unsubscribe(event_type, listener)
    @listeners[event_type].delete(listener)
  end
  
  def publish(event_type, data = {})
    listeners = @listeners[event_type]
    
    listeners.each do |listener|
      begin
        listener.call(event_type, data)
      rescue => e
        puts "Error in event listener: #{e.message}"
      end
    end
  end
  
  def clear_all
    @listeners.clear
  end
  
  def listener_count(event_type)
    @listeners[event_type].length
  end
  
  def total_listeners
    @listeners.values.sum(&:length)
  end
end

# Event types
module EventType
  ENTITY_CREATED = :entity_created
  ENTITY_DESTROYED = :entity_destroyed
  COLLISION = :collision
  GAME_OVER = :game_over
  LEVEL_COMPLETE = :level_complete
  INPUT_RECEIVED = :input_received
end
