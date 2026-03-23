# Physics Component - Represents physical properties

class Physics
  attr_accessor :solid, :gravity_affected, :mass, :friction
  
  def initialize(solid = false, gravity_affected = false, mass = 1.0, friction = 0.1)
    @solid = solid
    @gravity_affected = gravity_affected
    @mass = mass
    @friction = friction
  end
  
  def solid?
    @solid
  end
  
  def gravity_affected?
    @gravity_affected
  end
  
  def set_solid(solid)
    @solid = solid
  end
  
  def set_gravity_affected(affected)
    @gravity_affected = affected
  end
  
  def clone
    Physics.new(@solid, @gravity_affected, @mass, @friction)
  end
  
  def to_s
    "solid:#{@solid}, gravity:#{@gravity_affected}, mass:#{@mass}"
  end
end
