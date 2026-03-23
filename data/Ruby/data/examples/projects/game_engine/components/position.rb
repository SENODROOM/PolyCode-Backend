# Position Component - Represents position in 2D space

class Position
  attr_accessor :x, :y
  
  def initialize(x = 0, y = 0)
    @x = x
    @y = y
  end
  
  def set(x, y)
    @x = x
    @y = y
  end
  
  def move(dx, dy)
    @x += dx
    @y += dy
  end
  
  def distance_to(other)
    dx = @x - other.x
    dy = @y - other.y
    Math.sqrt(dx * dx + dy * dy)
  end
  
  def clone
    Position.new(@x, @y)
  end
  
  def to_s
    "(#{@x}, #{@y})"
  end
end
