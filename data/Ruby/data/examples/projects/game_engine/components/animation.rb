# Animation Component - Handles animation states

class Animation
  attr_accessor :current_frame, :frame_time, :animation_speed, :looping
  
  def initialize(frames = ['*'], animation_speed = 0.1, looping = true)
    @frames = frames
    @current_frame = 0
    @frame_time = 0.0
    @animation_speed = animation_speed
    @looping = looping
    @playing = true
  end
  
  def update(delta_time)
    return unless @playing
    
    @frame_time += delta_time
    
    if @frame_time >= @animation_speed
      @frame_time = 0.0
      next_frame
    end
  end
  
  def next_frame
    @current_frame += 1
    
    if @current_frame >= @frames.length
      if @looping
        @current_frame = 0
      else
        @current_frame = @frames.length - 1
        @playing = false
      end
    end
  end
  
  def current_frame_char
    @frames[@current_frame]
  end
  
  def play
    @playing = true
  end
  
  def pause
    @playing = false
  end
  
  def stop
    @playing = false
    @current_frame = 0
    @frame_time = 0.0
  end
  
  def reset
    @current_frame = 0
    @frame_time = 0.0
    @playing = true
  end
  
  def is_playing?
    @playing
  end
  
  def is_finished?
    !@looping && @current_frame >= @frames.length - 1
  end
  
  def set_frames(frames)
    @frames = frames
    @current_frame = 0
    @frame_time = 0.0
  end
  
  def clone
    Animation.new(@frames.dup, @animation_speed, @looping)
  end
  
  def to_s
    "#{current_frame_char} (frame #{@current_frame + 1}/#{@frames.length})"
  end
end
