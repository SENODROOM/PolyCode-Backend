# Health Component - Represents health/life points

class Health
  attr_accessor :current_health, :max_health, :invulnerable
  
  def initialize(max_health = 100)
    @max_health = max_health
    @current_health = max_health
    @invulnerable = false
  end
  
  def take_damage(amount)
    return false if @invulnerable || @current_health <= 0
    
    @current_health = [@current_health - amount, 0].max
    @current_health <= 0
  end
  
  def heal(amount)
    @current_health = [@current_health + amount, @max_health].min
  end
  
  def is_alive?
    @current_health > 0
  end
  
  def is_dead?
    @current_health <= 0
  end
  
  def health_percentage
    (@current_health.to_f / @max_health * 100).round(1)
  end
  
  def set_invulnerable(duration)
    @invulnerable = true
    # In a real implementation, you'd set a timer to remove invulnerability
  end
  
  def remove_invulnerability
    @invulnerable = false
  end
  
  def reset
    @current_health = @max_health
    @invulnerable = false
  end
  
  def clone
    Health.new(@max_health)
  end
  
  def to_s
    "#{@current_health}/#{@max_health} HP"
  end
end
