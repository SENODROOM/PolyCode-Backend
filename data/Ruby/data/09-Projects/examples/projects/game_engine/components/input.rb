# Input Component - Represents input handling

class Input
  attr_accessor :input_type, :enabled
  
  def initialize(input_type = :none, enabled = true)
    @input_type = input_type
    @enabled = enabled
  end
  
  def player?
    @input_type == :player
  end
  
  def ai?
    @input_type == :ai
  end
  
  def enabled?
    @enabled
  end
  
  def enable
    @enabled = true
  end
  
  def disable
    @enabled = false
  end
  
  def clone
    Input.new(@input_type, @enabled)
  end
  
  def to_s
    "#{@input_type} (#{@enabled ? 'enabled' : 'disabled'})"
  end
end
