# Velocity Component - Represents velocity and movement

class Velocity
  attr_accessor :vx, :vy, :max_speed
  
  def initialize(vx = 0, vy = 0, max_speed = 10.0)
    @vx = vx
    @vy = vy
    @max_speed = max_speed
  end
  
  def set(vx, vy)
    @vx = vx
    @vy = vy
    limit_speed
  end
  
  def accelerate(ax, ay)
    @vx += ax
    @vy += ay
    limit_speed
  end
  
  def apply_friction(friction = 0.95)
    @vx *= friction
    @vy *= friction
    
    # Stop if velocity is very small
    @vx = 0 if @vx.abs < 0.01
    @vy = 0 if @vy.abs < 0.01
  end
  
  def magnitude
    Math.sqrt(@vx * @vx + @vy * @vy)
  end
  
  def normalize
    mag = magnitude
    if mag > 0
      @vx /= mag
      @vy /= mag
    end
  end
  
  def clone
    Velocity.new(@vx, @vy, @max_speed)
  end
  
  def to_s
    "(#{@vx.round(2)}, #{@vy.round(2)})"
  end
  
  private
  
  def limit_speed
    current_speed = magnitude
    if current_speed > @max_speed
      normalize
      @vx *= @max_speed
      @vy *= @max_speed
    end
  end
end
