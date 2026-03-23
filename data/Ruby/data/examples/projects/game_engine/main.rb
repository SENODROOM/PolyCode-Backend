#!/usr/bin/env ruby

# Game Engine - Main entry point
# A simple 2D game engine with ECS architecture

require_relative 'game_engine'
require_relative 'game_loop'
require_relative 'entity_manager'
require_relative 'components/position'
require_relative 'components/velocity'
require_relative 'components/render'
require_relative 'components/physics'
require_relative 'components/input'
require_relative 'systems/movement_system'
require_relative 'systems/render_system'
require_relative 'systems/physics_system'
require_relative 'systems/input_system'

def main
  puts "Simple Ruby Game Engine"
  puts "======================="
  puts "Controls:"
  puts "  Arrow Keys: Move player"
  puts "  Space: Jump"
  puts "  R: Reset game"
  puts "  Q: Quit game"
  puts
  
  # Create game engine
  engine = GameEngine.new
  
  # Setup game
  setup_game(engine)
  
  # Start game loop
  engine.start
  
rescue Interrupt
  puts "\nGame stopped by user"
rescue => e
  puts "Error: #{e.message}"
  puts e.backtrace if ENV['DEBUG']
  exit 1
end

def setup_game(engine)
  entity_manager = engine.entity_manager
  
  # Create player entity
  player = entity_manager.create_entity
  entity_manager.add_component(player, Position.new(10, 10))
  entity_manager.add_component(player, Velocity.new(0, 0))
  entity_manager.add_component(player, Render.new('@', 'white'))
  entity_manager.add_component(player, Physics.new(false))
  entity_manager.add_component(player, Input.new(:player))
  
  # Create obstacles
  obstacles = [
    { x: 20, y: 5, char: '#', color: 'red' },
    { x: 15, y: 15, char: '#', color: 'red' },
    { x: 30, y: 8, char: '#', color: 'red' },
    { x: 25, y: 12, char: '#', color: 'red' },
    { x: 35, y: 20, char: '#', color: 'red' }
  ]
  
  obstacles.each do |obs|
    obstacle = entity_manager.create_entity
    entity_manager.add_component(obstacle, Position.new(obs[:x], obs[:y]))
    entity_manager.add_component(obstacle, Render.new(obs[:char], obs[:color]))
    entity_manager.add_component(obstacle, Physics.new(true))
  end
  
  # Create collectibles
  collectibles = [
    { x: 5, y: 5, char: '*', color: 'yellow' },
    { x: 40, y: 15, char: '*', color: 'yellow' },
    { x: 15, y: 25, char: '*', color: 'yellow' },
    { x: 30, y: 3, char: '*', color: 'yellow' }
  ]
  
  collectibles.each do |col|
    collectible = entity_manager.create_entity
    entity_manager.add_component(collectible, Position.new(col[:x], col[:y]))
    entity_manager.add_component(collectible, Render.new(col[:char], col[:color]))
  end
  
  # Create walls (boundaries)
  walls = [
    { x: 0, y: 0, char: '═', color: 'blue' },
    { x: 50, y: 0, char: '═', color: 'blue' },
    { x: 0, y: 30, char: '═', color: 'blue' },
    { x: 50, y: 30, char: '═', color: 'blue' }
  ]
  
  walls.each do |wall|
    wall_entity = entity_manager.create_entity
    entity_manager.add_component(wall_entity, Position.new(wall[:x], wall[:y]))
    entity_manager.add_component(wall_entity, Render.new(wall[:char], wall[:color]))
    entity_manager.add_component(wall_entity, Physics.new(true))
  end
  
  # Add vertical walls
  (1..29).each do |y|
    left_wall = entity_manager.create_entity
    entity_manager.add_component(left_wall, Position.new(0, y))
    entity_manager.add_component(left_wall, Render.new('║', 'blue'))
    entity_manager.add_component(left_wall, Physics.new(true))
    
    right_wall = entity_manager.create_entity
    entity_manager.add_component(right_wall, Position.new(50, y))
    entity_manager.add_component(right_wall, Render.new('║', 'blue'))
    entity_manager.add_component(right_wall, Physics.new(true))
  end
end

# Run the game
main if __FILE__ == $0
