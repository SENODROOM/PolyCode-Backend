# Render Component - Represents visual appearance

class Render
  attr_accessor :char, :color, :visible
  
  def initialize(char = '*', color = 'white', visible = true)
    @char = char
    @color = color
    @visible = visible
  end
  
  def set_char(char)
    @char = char
  end
  
  def set_color(color)
    @color = color
  end
  
  def show
    @visible = true
  end
  
  def hide
    @visible = false
  end
  
  def visible?
    @visible
  end
  
  def clone
    Render.new(@char, @color, @visible)
  end
  
  def to_s
    "#{@char} (#{@color})"
  end
end
