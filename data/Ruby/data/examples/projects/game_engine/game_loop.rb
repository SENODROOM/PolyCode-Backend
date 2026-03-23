# Game Loop - Fixed timestep game loop implementation

class GameLoop
  TARGET_FPS = 60
  FRAME_TIME = 1.0 / TARGET_FPS
  MAX_FRAME_TIME = 0.25
  
  def initialize(game_engine)
    @game_engine = game_engine
    @running = false
    @accumulator = 0.0
    @last_time = Time.now
  end
  
  def run
    @running = true
    
    while @running
      current_time = Time.now
      delta_time = current_time - @last_time
      @last_time = current_time
      
      # Prevent spiral of death
      delta_time = [delta_time, MAX_FRAME_TIME].min
      
      @accumulator += delta_time
      
      # Fixed timestep update
      while @accumulator >= FRAME_TIME
        update(FRAME_TIME)
        @accumulator -= FRAME_TIME
      end
      
      # Render
      render
      
      # Handle input
      @game_engine.handle_input
      
      # Simple frame rate limiting
      sleep_time = FRAME_TIME - (Time.now - current_time)
      sleep(sleep_time) if sleep_time > 0
    end
  end
  
  def stop
    @running = false
  end
  
  private
  
  def update(delta_time)
    @game_engine.update(delta_time)
  end
  
  def render
    @game_engine.render
  end
end
