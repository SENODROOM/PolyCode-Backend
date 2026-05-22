# Game Engine - Core engine class

class GameEngine
  attr_reader :entity_manager, :systems, :running, :input_handler
  
  def initialize
    @entity_manager = EntityManager.new
    @systems = []
    @running = false
    @input_handler = InputHandler.new
    @game_state = GameState.new
    @renderer = ConsoleRenderer.new(51, 31)
    
    setup_systems
  end
  
  def start
    @running = true
    game_loop = GameLoop.new(self)
    game_loop.run
  end
  
  def stop
    @running = false
  end
  
  def update(delta_time)
    # Update all systems
    @systems.each do |system|
      system.update(@entity_manager, delta_time)
    end
    
    # Check game state
    check_game_state
  end
  
  def render
    @renderer.clear
    @renderer.render(@entity_manager)
    @renderer.display
  end
  
  def handle_input
    @input_handler.update
    
    if @input_handler.key_pressed?(:q)
      stop
    elsif @input_handler.key_pressed?(:r)
      reset_game
    end
  end
  
  def reset_game
    @entity_manager.clear_all
    setup_game_entities
    @game_state.reset
  end
  
  private
  
  def setup_systems
    # Add systems in order of execution
    @systems << InputSystem.new(@input_handler)
    @systems << MovementSystem.new
    @systems << PhysicsSystem.new
    @systems << RenderSystem.new(@renderer)
  end
  
  def setup_game_entities
    # This would be called from main.rb
    # For now, entities are created in main.rb
  end
  
  def check_game_state
    # Check for win/lose conditions
    player = find_player
    return unless player
    
    # Check collision with collectibles
    check_collectible_collision(player)
    
    # Check if all collectibles collected
    check_win_condition
  end
  
  def find_player
    entities = @entity_manager.get_entities_with_components([Input, Position])
    entities.first
  end
  
  def check_collectible_collision(player)
    player_pos = @entity_manager.get_component(player, Position)
    collectibles = @entity_manager.get_entities_with_components([Position, Render])
    
    collectibles.each do |entity|
      next if @entity_manager.has_component?(entity, Input) || @entity_manager.has_component?(entity, Physics)
      
      entity_pos = @entity_manager.get_component(entity, Position)
      render_comp = @entity_manager.get_component(entity, Render)
      
      # Simple collision detection
      if player_pos.x == entity_pos.x && player_pos.y == entity_pos.y
        @game_state.collect_item
        @entity_manager.remove_entity(entity)
        puts "Item collected! Score: #{@game_state.score}"
      end
    end
  end
  
  def check_win_condition
    collectibles = @entity_manager.get_entities_with_components([Position, Render])
      .reject { |entity| @entity_manager.has_component?(entity, Input) }
      .reject { |entity| @entity_manager.has_component?(entity, Physics) }
    
    if collectibles.empty?
      puts "Congratulations! You collected all items!"
      puts "Final score: #{@game_state.score}"
      puts "Press R to play again or Q to quit"
    end
  end
end

class GameState
  attr_reader :score, :items_collected, :total_items
  
  def initialize
    reset
  end
  
  def reset
    @score = 0
    @items_collected = 0
    @total_items = 0
  end
  
  def collect_item
    @items_collected += 1
    @score += 10
  end
  
  def set_total_items(count)
    @total_items = count
  end
end

class InputHandler
  def initialize
    @keys_pressed = Set.new
  end
  
  def update
    # In a real implementation, this would read from actual input
    # For now, we'll simulate with keyboard input
    begin
      if STDIN.ready?
        input = STDIN.getch
        handle_key_input(input)
      end
    rescue
      # No input available
    end
  end
  
  def key_pressed?(key)
    @keys_pressed.include?(key)
  end
  
  private
  
  def handle_key_input(input)
    case input
    when 'w', 'W'
      @keys_pressed.add(:up)
    when 's', 'S'
      @keys_pressed.add(:down)
    when 'a', 'A'
      @keys_pressed.add(:left)
    when 'd', 'D'
      @keys_pressed.add(:right)
    when ' '
      @keys_pressed.add(:space)
    when 'r', 'R'
      @keys_pressed.add(:r)
    when 'q', 'Q'
      @keys_pressed.add(:q)
    end
  end
end

class ConsoleRenderer
  def initialize(width, height)
    @width = width
    @height = height
    @buffer = Array.new(@height) { Array.new(@width, ' ') }
  end
  
  def clear
    @buffer = Array.new(@height) { Array.new(@width, ' ') }
  end
  
  def render(entity_manager)
    entities = entity_manager.get_entities_with_components([Position, Render])
    
    entities.each do |entity|
      position = entity_manager.get_component(entity, Position)
      render = entity_manager.get_component(entity, Render)
      
      x = position.x.clamp(0, @width - 1)
      y = position.y.clamp(0, @height - 1)
      
      @buffer[y][x] = render.char
    end
  end
  
  def display
    system('clear') || system('cls')
    
    puts "╔" + "═" * (@width - 2) + "╗"
    
    @buffer.each_with_index do |row, y|
      if y == 0 || y == @height - 1
        next
      end
      
      print "║"
      row.each_with_index do |char, x|
        if x == 0 || x == @width - 1
          print "║"
        else
          print char
        end
      end
      puts "║"
    end
    
    puts "╚" + "═" * (@width - 2) + "╝"
    puts
    puts "Use WASD to move, Space to jump, R to reset, Q to quit"
  end
end

# Simple Set implementation for compatibility
class Set
  def initialize
    @items = []
  end
  
  def add(item)
    @items << item unless @items.include?(item)
  end
  
  def include?(item)
    @items.include?(item)
  end
  
  def delete(item)
    @items.delete(item)
  end
  
  def clear
    @items.clear
  end
  
  def empty?
    @items.empty?
  end
end
