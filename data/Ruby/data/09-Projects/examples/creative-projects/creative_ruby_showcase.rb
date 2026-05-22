# Creative Ruby Showcase - Demonstrates Ruby's creative capabilities
# This file showcases the most creative and innovative uses of Ruby

require 'json'
require 'securerandom'
require 'base64'

class CreativeRubyShowcase
  def initialize
    @showcase_items = []
    @current_index = 0
  end
  
  def start_showcase
    puts "🎨 Creative Ruby Showcase"
    puts "========================"
    puts "Welcome to the most creative Ruby demonstrations!"
    puts ""
    
    interactive_showcase
  end
  
  def demonstrate_metaprogramming_magic
    puts "🪄 Metaprogramming Magic"
    puts "======================="
    puts "Watch Ruby create methods dynamically!"
    puts ""
    
    # Dynamic class creation
    dynamic_class = create_dynamic_class("MagicClass", [:fly, :teleport, :invisible])
    
    # Dynamic method creation
    dynamic_instance = dynamic_class.new
    dynamic_instance.learn_ability("time_travel")
    
    # Method missing magic
    puts "Calling undefined method..."
    begin
      dynamic_instance.any_method_call("super_punch")
    rescue NoMethodError => e
      puts "Caught: #{e.message}"
    end
    
    # Dynamic method generation
    generate_magic_methods(dynamic_class)
    
    puts "✨ Metaprogramming magic complete!"
    puts ""
    
    dynamic_class
  end
  
  def demonstrate_artistic_generators
    puts "🎨 Artistic Generators"
    puts "====================="
    puts "Creating art with algorithms!"
    puts ""
    
    # ASCII art generator
    puts "🖼️  ASCII Art:"
    ascii_art = generate_ascii_art("RUBY")
    puts ascii_art
    puts ""
    
    # Poetry generator
    puts "📝 Ruby Poetry:"
    poem = generate_ruby_poem
    puts poem
    puts ""
    
    # Music generator
    puts "🎵 Musical Pattern:"
    music_pattern = generate_musical_pattern
    puts music_pattern
    puts ""
    
    # Color palette generator
    puts "🎨 Color Palette:"
    palette = generate_color_palette
    palette.each_with_index do |color, i|
      puts "  Color #{i + 1}: #{color[:name]} (#{color[:hex]})"
    end
    puts ""
    
    {
      ascii_art: ascii_art,
      poem: poem,
      music_pattern: music_pattern,
      color_palette: palette
    }
  end
  
  def demonstrate_dsl_creation
    puts "🏗️  DSL (Domain Specific Language) Creation"
    puts "=========================================="
    puts "Creating mini-languages with Ruby!"
    puts ""
    
    # Game DSL
    puts "🎮 Game Creation DSL:"
    game = create_game_with_dsl
    puts "Created game: #{game[:name]}"
    puts "  Characters: #{game[:characters].join(', ')}"
    puts "  World: #{game[:world]}"
    puts ""
    
    # Recipe DSL
    puts "🍳 Recipe DSL:"
    recipe = create_recipe_with_dsl
    puts "Recipe: #{recipe[:name]}"
    puts "  Ingredients: #{recipe[:ingredients].join(', ')}"
    puts "  Steps: #{recipe[:steps].length}"
    puts ""
    
    # Testing DSL
    puts "🧪 Testing DSL:"
    test_results = run_testing_dsl
    puts "Tests run: #{test_results[:total]}"
    puts "Passed: #{test_results[:passed]}"
    puts "Failed: #{test_results[:failed]}"
    puts ""
    
    {
      game: game,
      recipe: recipe,
      test_results: test_results
    }
  end
  
  def demonstrate_functional_programming
    puts "🔢 Functional Programming in Ruby"
    puts "================================="
    puts "Ruby's functional capabilities!"
    puts ""
    
    # Higher-order functions
    puts "🔢 Higher-order functions:"
    numbers = (1..10).to_a
    transformed = apply_functional_transformations(numbers)
    puts "Original: #{numbers}"
    puts "Transformed: #{transformed}"
    puts ""
    
    # Lazy evaluation simulation
    puts "⏳ Lazy evaluation:"
    lazy_sequence = create_lazy_sequence
    first_five = lazy_sequence.take(5)
    puts "First 5 of infinite sequence: #{first_five}"
    puts ""
    
    # Function composition
    puts "🔄 Function composition:"
    composed = compose_functions
    result = composed.call(10)
    puts "Composed function result: #{result}"
    puts ""
    
    # Memoization
    puts "💾 Memoization:"
    memoized_fib = memoize_fibonacci
    puts "Fibonacci(10): #{memoized_fib.call(10)}"
    puts "Fibonacci(20): #{memoized_fib.call(20)}"
    puts ""
    
    {
      transformations: transformed,
      lazy_sequence: first_five,
      composition: result,
      memoization: memoized_fib
    }
  end
  
  def demonstrate_concurrent_programming
    puts "⚡ Concurrent Programming"
    puts "========================="
    puts "Ruby's concurrency capabilities!"
    puts ""
    
    # Thread pool
    puts "🧵 Thread pool:"
    results = run_thread_pool_demo
    puts "Thread pool results: #{results}"
    puts ""
    
    # Actor model
    puts "🎭 Actor model:"
    actor_results = run_actor_model_demo
    puts "Actor results: #{actor_results}"
    puts ""
    
    # Concurrent data structures
    puts "🔗 Concurrent data structures:"
    concurrent_results = run_concurrent_structures_demo
    puts "Concurrent results: #{concurrent_results}"
    puts ""
    
    {
      thread_pool: results,
      actor_model: actor_results,
      concurrent_structures: concurrent_results
    }
  end
  
  def demonstrate_ai_algorithms
    puts "🤖 AI Algorithms in Ruby"
    puts "========================"
    puts "Implementing AI with pure Ruby!"
    puts ""
    
    # Neural network
    puts "🧠 Simple Neural Network:"
    nn_results = run_simple_neural_network
    puts "Neural network prediction: #{nn_results}"
    puts ""
    
    # Genetic algorithm
    puts "🧬 Genetic Algorithm:"
    ga_results = run_genetic_algorithm
    puts "Best solution: #{ga_results[:best]}"
    puts "Generations: #{ga_results[:generations]}"
    puts ""
    
    # Pathfinding
    puts "🗺️  Pathfinding (A*):"
    path_results = run_pathfinding_demo
    puts "Path found: #{path_results[:found]}"
    puts "Path length: #{path_results[:path_length]}"
    puts ""
    
    {
      neural_network: nn_results,
      genetic_algorithm: ga_results,
      pathfinding: path_results
    }
  end
  
  def demonstrate_creative_data_structures
    puts "🏗️  Creative Data Structures"
    puts "============================"
    puts "Innovative data structures in Ruby!"
    puts ""
    
    # Graph visualization
    puts "🕸️  Graph Visualization:"
    graph_viz = create_graph_visualization
    puts "Graph nodes: #{graph_viz[:nodes]}"
    puts "Graph edges: #{graph_viz[:edges]}"
    puts ""
    
    # Trie implementation
    puts "🌳 Trie Data Structure:"
    trie_results = demonstrate_trie
    puts "Words in trie: #{trie_results[:words]}"
    puts "Search results: #{trie_results[:search_results]}"
    puts ""
    
    # Bloom filter
    puts "🌸 Bloom Filter:"
    bloom_results = demonstrate_bloom_filter
    puts "Items added: #{bloom_results[:added]}"
    puts "False positives: #{bloom_results[:false_positives]}"
    puts ""
    
    {
      graph: graph_viz,
      trie: trie_results,
      bloom_filter: bloom_results
    }
  end
  
  def demonstrate_language_features
    puts "💎 Ruby Language Features"
    puts "========================"
    puts "Exploring Ruby's unique features!"
    puts ""
    
    # Pattern matching
    puts "🎯 Pattern Matching:"
    pattern_results = demonstrate_pattern_matching
    puts "Pattern matches: #{pattern_results}"
    puts ""
    
    # Method chaining
    puts "⛓️  Method Chaining:"
    chain_results = demonstrate_method_chaining
    puts "Chain result: #{chain_results}"
    puts ""
    
    # Symbol to proc
    puts "🔀 Symbol to Proc:"
    proc_results = demonstrate_symbol_to_proc
    puts "Proc results: #{proc_results}"
    puts ""
    
    # Splat operator
    puts "💫 Splat Operator:"
    splat_results = demonstrate_splat_operator
    puts "Splat results: #{splat_results}"
    puts ""
    
    {
      pattern_matching: pattern_results,
      method_chaining: chain_results,
      symbol_to_proc: proc_results,
      splat_operator: splat_results
    }
  end
  
  def demonstrate_performance_optimization
    puts "⚡ Performance Optimization"
    puts "============================"
    puts "Making Ruby code faster!"
    puts ""
    
    # Benchmarking
    puts "⏱️  Benchmarking:"
    benchmark_results = run_benchmarks
    puts "Fast method: #{benchmark_results[:fast]}ms"
    puts "Slow method: #{benchmark_results[:slow]}ms"
    puts "Improvement: #{benchmark_results[:improvement]}x"
    puts ""
    
    # Memory optimization
    puts "💾 Memory Optimization:"
    memory_results = demonstrate_memory_optimization
    puts "Memory usage: #{memory_results[:usage]}MB"
    puts "Optimized: #{memory_results[:optimized]}"
    puts ""
    
    # Caching
    puts "🗄️  Caching:"
    cache_results = demonstrate_caching
    puts "Cache hits: #{cache_results[:hits]}"
    puts "Cache misses: #{cache_results[:misses]}"
    puts ""
    
    {
      benchmarking: benchmark_results,
      memory_optimization: memory_results,
      caching: cache_results
    }
  end
  
  def create_creative_application
    puts "🎨 Creative Application Builder"
    puts "==============================="
    puts "Building something amazing with Ruby!"
    puts ""
    
    # Interactive story generator
    puts "📖 Interactive Story Generator:"
    story = generate_interactive_story
    puts "Story title: #{story[:title]}"
    puts "Story length: #{story[:content].length} words"
    puts ""
    
    # Procedural dungeon generator
    puts "🏰 Procedural Dungeon Generator:"
    dungeon = generate_dungeon
    puts "Dungeon size: #{dungeon[:width]}x#{dungeon[:height]}"
    puts "Rooms: #{dungeon[:rooms].length}"
    puts ""
    
    # Chatbot personality
    puts "💬 Chatbot Personality:"
    chatbot = create_chatbot_personality
    puts "Personality: #{chatbot[:personality]}"
    puts "Responses: #{chatbot[:responses].length}"
    puts ""
    
    {
      story: story,
      dungeon: dungeon,
      chatbot: chatbot
    }
  end
  
  private
  
  def create_dynamic_class(class_name, methods)
    # Create class dynamically
    new_class = Class.new do
      methods.each do |method|
        define_method(method) do
          puts "✨ Using #{method} ability!"
        end
      end
      
      def learn_ability(ability)
        self.class.define_method(ability) do
          puts "🎓 Learned new ability: #{ability}!"
        end
      end
      
      def method_missing(method_name, *args, &block)
        puts "🔮 Casting spell: #{method_name}!"
        super if method_name.to_s.start_with?("real_")
      end
      
      def respond_to_missing?(method_name, include_private = false)
        method_name.to_s.start_with?("real_") || super
      end
    end
    
    # Set class name
    Object.const_set(class_name, new_class)
    
    new_class
  end
  
  def generate_magic_methods(dynamic_class)
    # Generate methods with magic
    magic_verbs = ["sparkle", "glow", "shimmer", "float", "vanish"]
    
    magic_verbs.each do |verb|
      dynamic_class.define_method("#{verb}_intensely") do
        puts "✨ #{verb.capitalize}ing intensely!"
      end
      
      dynamic_class.define_method("#{verb}_with_style") do |style|
        puts "✨ #{verb.capitalize}ing with #{style} style!"
      end
    end
  end
  
  def generate_ascii_art(text)
    art_styles = {
      "RUBY" => [
        "  ____  ____  _     _    ____   ___  ____ ",
        " / ___||  _ \\| |   | |  |  _ \\ / _ \\|  _ \\",
        "| |  _| |_) | |   | |  | | | | | | | | | |",
        "| |_| |  _ <| |___| |  | |_| | |_| | |_| |",
        " \\____|_| \\_\\_____|_|  |____/ \\___/|____/ "
      ],
      "CREATIVE" => [
        "  ____  _____    _    ____  ____  _____ ",
        " / ___|| ____|  / \\  / ___||  _ \\| ____|",
        "| |  _| |  _|  / _ \\ \\___ \\| |_) |  _|  ",
        "| |_| | |___| / ___ \\ ___) |  _ <| |___ ",
        " \\____|_____/_/   \\_\\____/|_| \\_\\_____/"
      ]
    }
    
    art_styles[text] || ["ASCII art not available for #{text}"]
  end
  
  def generate_ruby_poem
    ruby_words = [
      "elegant", "dynamic", "expressive", "beautiful", "powerful",
      "flexible", "intuitive", "joyful", "creative", "magical"
    ]
    
    lines = []
    4.times do
      line = ruby_words.sample(3).join(" ")
      lines << line.capitalize
    end
    
    lines.join("\n")
  end
  
  def generate_musical_pattern
    notes = ["C", "D", "E", "F", "G", "A", "B"]
    durations = ["whole", "half", "quarter", "eighth"]
    
    pattern = []
    8.times do
      pattern << "#{notes.sample} #{durations.sample}"
    end
    
    pattern.join(" | ")
  end
  
  def generate_color_palette
    color_schemes = [
      { name: "Sunset", colors: ["#FF6B6B", "#FFE66D", "#4ECDC4", "#95E1D3"] },
      { name: "Ocean", colors: ["#0077BE", "#00A8CC", "#74C0FC", "#B4E7CE"] },
      { name: "Forest", colors: ["#2D5016", "#73A942", "#AAD576", "#C5E99B"] },
      { name: "Cosmic", colors: ["#6A0572", "#AB83A1", "#C77DFF", "#E7C6FF"] }
    ]
    
    scheme = color_schemes.sample
    
    scheme[:colors].map.with_index do |color, i|
      {
        name: "#{scheme[:name]} #{i + 1}",
        hex: color,
        rgb: hex_to_rgb(color)
      }
    end
  end
  
  def hex_to_rgb(hex)
    r = hex[1..2].to_i(16)
    g = hex[3..4].to_i(16)
    b = hex[5..6].to_i(16)
    [r, g, b]
  end
  
  def create_game_with_dsl
    # Game DSL implementation
    game_builder = Class.new do
      def self.create(name, &block)
        game = { name: name, characters: [], world: nil, items: [] }
        
        game_definition = Class.new do
          define_method(:character) do |name, traits: []|
            game[:characters] << { name: name, traits: traits }
          end
          
          define_method(:world) do |description|
            game[:world] = description
          end
          
          define_method(:item) do |name, power: 1|
            game[:items] << { name: name, power: power }
          end
        end
        
        game_definition.new.instance_eval(&block) if block_given?
        game
      end
    end
    
    game_builder.create("Ruby Quest") do
      character "Ruby Warrior", traits: ["brave", "wise"]
      character "Gem Guardian", traits: ["magical", "ancient"]
      world "A mystical land where code comes to life"
      item "Ruby Sword", power: 100
      item "Shield of Blocks", power: 50
    end
  end
  
  def create_recipe_with_dsl
    # Recipe DSL implementation
    recipe_builder = Class.new do
      def self.create(name, &block)
        recipe = { name: name, ingredients: [], steps: [], time: 0 }
        
        recipe_definition = Class.new do
          define_method(:ingredient) do |name, amount: "1 cup"|
            recipe[:ingredients] << "#{name} - #{amount}"
          end
          
          define_method(:step) do |description|
            recipe[:steps] << description
          end
          
          define_method(:cook_time) do |minutes|
            recipe[:time] = minutes
          end
        end
        
        recipe_definition.new.instance_eval(&block) if block_given?
        recipe
      end
    end
    
    recipe_builder.create("Ruby Cake") do
      ingredient "Ruby flour", amount: "2 cups"
      ingredient "Gem sugar", amount: "1 cup"
      ingredient "Method eggs", amount: "3"
      
      step "Mix flour and sugar in a bowl"
      step "Add eggs and mix until smooth"
      step "Bake at 350 degrees for 30 minutes"
      
      cook_time 30
    end
  end
  
  def run_testing_dsl
    # Testing DSL implementation
    test_framework = Class.new do
      def self.run(name, &block)
        results = { total: 0, passed: 0, failed: 0 }
        
        test_definition = Class.new do
          define_method(:test) do |description, &test_block|
            results[:total] += 1
            
            begin
              test_block.call
              results[:passed] += 1
              puts "✅ #{description}"
            rescue => e
              results[:failed] += 1
              puts "❌ #{description}: #{e.message}"
            end
          end
          
          define_method(:expect) do |actual|
            Expectation.new(actual)
          end
        end
        
        test_definition.new.instance_eval(&block) if block_given?
        results
      end
    end
    
    class Expectation
      def initialize(actual)
        @actual = actual
      end
      
      def to_eq(expected)
        raise "Expected #{expected}, got #{@actual}" unless @actual == expected
      end
      
      def to_be_truthy
        raise "Expected truthy, got #{@actual}" unless @actual
      end
    end
    
    test_framework.run("Ruby Tests") do
      test "addition works" do
        expect(2 + 2).to_eq(4)
      end
      
      test "string concatenation" do
        expect("Ruby" + " " + "Rocks").to_eq("Ruby Rocks")
      end
      
      test "array includes element" do
        expect([1, 2, 3].include?(2)).to_be_truthy
      end
    end
  end
  
  def apply_functional_transformations(numbers)
    # Functional transformations
    transformations = [
      ->(x) { x * 2 },
      ->(x) { x + 1 },
      ->(x) { x ** 2 },
      ->(x) { x / 2.0 }
    ]
    
    numbers.reduce(numbers) do |acc, transform|
      acc.map(&transform)
    end
  end
  
  def create_lazy_sequence
    # Lazy sequence simulation
    Enumerator.new do |yielder|
      n = 0
      loop do
        yielder << n
        n += 1
        sleep(0.01)  # Simulate computation
      end
    end
  end
  
  def compose_functions
    # Function composition
    add_one = ->(x) { x + 1 }
    multiply_two = ->(x) { x * 2 }
    square = ->(x) { x ** 2 }
    
    # Compose functions
    add_one.then(multiply_two).then(square)
  end
  
  def memoize_fibonacci
    # Memoized Fibonacci
    cache = {}
    
    ->(n) do
      return cache[n] if cache[n]
      
      result = if n <= 1
        n
      else
        call(n - 1) + call(n - 2)
      end
      
      cache[n] = result
      result
    end
  end
  
  def run_thread_pool_demo
    # Thread pool demonstration
    results = []
    threads = []
    
    5.times do |i|
      threads << Thread.new do
        sleep(rand(0.1..0.5))
        results << "Task #{i} completed by #{Thread.current.object_id}"
      end
    end
    
    threads.each(&:join)
    results
  end
  
  def run_actor_model_demo
    # Actor model simulation
    actors = []
    
    3.times do |i|
      actors << Thread.new do
        messages = []
        
        5.times do |j|
          messages << "Actor #{i} received message #{j}"
          sleep(0.1)
        end
        
        messages
      end
    end
    
    actors.map(&:value).flatten
  end
  
  def run_concurrent_structures_demo
    # Concurrent data structures
    queue = Queue.new
    results = []
    
    # Producer threads
    producers = 2.times.map do |i|
      Thread.new do
        5.times do |j|
          queue << "Item #{i}-#{j}"
          sleep(0.05)
        end
      end
    end
    
    # Consumer thread
    consumer = Thread.new do
      while !queue.empty? || producers.any?(&:alive?)
        unless queue.empty?
          results << queue.pop(true)
        end
        sleep(0.01)
      end
    end
    
    producers.each(&:join)
    consumer.join
    
    results
  end
  
  def run_simple_neural_network
    # Very simple neural network
    weights = [0.5, -0.3, 0.8]
    bias = 0.1
    
    # Forward pass
    inputs = [1.0, 0.5, -0.2]
    weighted_sum = inputs.zip(weights).sum { |x, w| x * w } + bias
    output = 1 / (1 + Math.exp(-weighted_sum))  # Sigmoid
    
    (output > 0.5) ? "Positive" : "Negative"
  end
  
  def run_genetic_algorithm
    # Simple genetic algorithm
    population = Array.new(10) { Array.new(5) { rand(0..1) } }
    generations = 50
    
    generations.times do |gen|
      # Fitness function (count of 1s)
      fitness = population.map { |ind| ind.count(1) }
      
      # Selection and reproduction
      new_population = []
      5.times do
        parent1 = population.max_by { |ind| ind.count(1) }
        parent2 = population.max_by { |ind| ind.count(1) }
        
        child = parent1.zip(parent2).map { |a, b| rand < 0.5 ? a : b }
        
        # Mutation
        child.map! { |gene| rand < 0.1 ? 1 - gene : gene }
        
        new_population << child
      end
      
      population = new_population
    end
    
    best = population.max_by { |ind| ind.count(1) }
    
    {
      best: best,
      generations: generations,
      fitness: best.count(1)
    }
  end
  
  def run_pathfinding_demo
    # Simple A* pathfinding
    grid = [
      [0, 0, 0, 0, 0],
      [0, 1, 1, 1, 0],
      [0, 0, 0, 1, 0],
      [0, 1, 0, 0, 0],
      [0, 0, 0, 0, 0]
    ]
    
    start = [0, 0]
    goal = [4, 4]
    
    # Simplified pathfinding (just check if path exists)
    path = []
    current = start.dup
    
    while current != goal && path.length < 25
      next_step = find_next_step(grid, current, goal)
      break unless next_step
      
      path << next_step
      current = next_step
    end
    
    {
      found: current == goal,
      path_length: path.length,
      path: path
    }
  end
  
  def find_next_step(grid, current, goal)
    # Find next step towards goal
    directions = [[0, 1], [1, 0], [0, -1], [-1, 0]]
    
    directions.each do |dx, dy|
      next_x = current[0] + dx
      next_y = current[1] + dy
      
      if next_x >= 0 && next_x < grid.length &&
         next_y >= 0 && next_y < grid.first.length &&
         grid[next_x][next_y] == 0
        
        return [next_x, next_y]
      end
    end
    
    nil
  end
  
  def create_graph_visualization
    # Simple graph representation
    nodes = ["A", "B", "C", "D", "E"]
    edges = [
      ["A", "B"], ["A", "C"], ["B", "D"],
      ["C", "D"], ["D", "E"], ["E", "A"]
    ]
    
    # Simple ASCII visualization
    visualization = "Graph Visualization:\n"
    edges.each do |from, to|
      visualization += "#{from} -- #{to}\n"
    end
    
    {
      nodes: nodes,
      edges: edges,
      visualization: visualization
    }
  end
  
  def demonstrate_trie
    # Trie implementation
    trie = {}
    
    words = ["ruby", "rails", "gem", "programming", "code"]
    
    words.each do |word|
      current = trie
      word.each_char do |char|
        current[char] ||= {}
        current = current[char]
      end
      current[:end] = true
    end
    
    # Search function
    search_results = []
    words.each do |word|
      found = search_trie(trie, word)
      search_results << "#{word}: #{found ? 'Found' : 'Not found'}"
    end
    
    {
      words: words,
      search_results: search_results,
      trie: trie
    }
  end
  
  def search_trie(trie, word)
    current = trie
    
    word.each_char do |char|
      return false unless current[char]
      current = current[char]
    end
    
    current[:end] || false
  end
  
  def demonstrate_bloom_filter
    # Simple bloom filter
    size = 100
    filter = Array.new(size, false)
    hash_functions = [->(x) { x.hash % size }, ->(x) { (x.hash * 7) % size }]
    
    items = ["ruby", "rails", "gem", "code"]
    false_positives = 0
    
    # Add items
    items.each do |item|
      hash_functions.each { |hash| filter[hash.call(item)] = true }
    end
    
    # Test items
    test_items = items + ["python", "java", "javascript"]
    
    test_items.each do |item|
      exists = hash_functions.all? { |hash| filter[hash.call(item)] }
      
      if exists && !items.include?(item)
        false_positives += 1
      end
    end
    
    {
      added: items.length,
      false_positives: false_positives,
      filter_size: size
    }
  end
  
  def demonstrate_pattern_matching
    # Pattern matching simulation (Ruby 2.7+ style)
    results = []
    
    data = [
      { type: :user, name: "Alice", age: 25 },
      { type: :admin, name: "Bob", permissions: ["read", "write"] },
      { type: :guest, name: "Charlie" }
    ]
    
    data.each do |item|
      case item[:type]
      when :user
        results << "User: #{item[:name]} (#{item[:age]} years old)"
      when :admin
        results << "Admin: #{item[:name]} (#{item[:permissions].join(', ')})"
      when :guest
        results << "Guest: #{item[:name]}"
      end
    end
    
    results
  end
  
  def demonstrate_method_chaining
    # Method chaining demonstration
    result = (1..10)
      .select { |x| x.even? }
      .map { |x| x * 2 }
      .reject { |x| x > 10 }
      .sum
    
    result
  end
  
  def demonstrate_symbol_to_proc
    # Symbol to proc demonstration
    words = ["ruby", "rails", "gem", "code"]
    
    results = {
      upcase: words.map(&:upcase),
      lengths: words.map(&:length),
      reversed: words.map(&:reverse)
    }
    
    results
  end
  
  def demonstrate_splat_operator
    # Splat operator demonstration
    def sum(*numbers)
      numbers.sum
    end
    
    def process_data(**options)
      options.map { |k, v| "#{k}: #{v}" }
    end
    
    results = {
      array_splat: sum(1, 2, 3, 4, 5),
      hash_splat: process_data(name: "Ruby", version: "3.0", type: "Language")
    }
    
    results
  end
  
  def run_benchmarks
    # Simple benchmarking
    require 'benchmark'
    
    slow_method = -> do
      10000.times { |i| i * 2 }
    end
    
    fast_method = -> do
      (0..10000).map { |i| i * 2 }
    end
    
    slow_time = Benchmark.realtime { slow_method.call } * 1000
    fast_time = Benchmark.realtime { fast_method.call } * 1000
    
    improvement = (slow_time / fast_time).round(2)
    
    {
      slow: slow_time.round(2),
      fast: fast_time.round(2),
      improvement: improvement
    }
  end
  
  def demonstrate_memory_optimization
    # Memory optimization demonstration
    large_array = Array.new(100000) { rand(1000) }
    
    # Before optimization
    before_usage = large_array.size * 8  # Rough estimate
    
    # After optimization (use Set for uniqueness)
    unique_values = large_array.uniq
    after_usage = unique_values.size * 8
    
    {
      usage: (before_usage / 1024.0 / 1024.0).round(2),
      optimized: after_usage < before_usage
    }
  end
  
  def demonstrate_caching
    # Simple caching demonstration
    cache = {}
    
    expensive_operation = lambda do |x|
      cache[x] ||= begin
        sleep(0.01)  # Simulate expensive operation
        x * x
      end
    end
    
    hits = 0
    misses = 0
    
    10.times do |i|
      result = expensive_operation.call(i)
      if cache[i]
        hits += 1
      else
        misses += 1
      end
    end
    
    # Call again to test cache
    5.times do |i|
      expensive_operation.call(i)
      hits += 1
    end
    
    {
      hits: hits,
      misses: misses
    }
  end
  
  def generate_interactive_story
    # Interactive story generator
    story_templates = [
      {
        title: "The Ruby Adventure",
        beginning: "In a land where code comes to life, a brave programmer sets out on a quest...",
        middle: "Along the way, they encounter magical gems and mysterious methods...",
        ending: "Finally, they discover the legendary Ruby of infinite power!"
      },
      {
        title: "The Gem Collector",
        beginning: "A young developer dreams of collecting all the precious gems in the world...",
        middle: "Each gem holds a unique power, but collecting them requires solving puzzles...",
        ending: "With all gems collected, they become the master of Ruby magic!"
      }
    ]
    
    template = story_templates.sample
    
    story = {
      title: template[:title],
      content: template[:beginning] + " " + template[:middle] + " " + template[:ending],
      choices: ["Fight the bug", "Use a gem", "Write more code", "Refactor"]
    }
    
    story
  end
  
  def generate_dungeon
    # Procedural dungeon generator
    width = 20
    height = 20
    dungeon = Array.new(height) { Array.new(width, 1) }  # 1 = wall
    
    rooms = []
    
    # Generate rooms
    5.times do
      room_width = rand(3..6)
      room_height = rand(3..6)
      room_x = rand(1..width - room_width - 1)
      room_y = rand(1..height - room_height - 1)
      
      # Carve out room
      room_height.times do |y|
        room_width.times do |x|
          dungeon[room_y + y][room_x + x] = 0  # 0 = floor
        end
      end
      
      rooms << {
        x: room_x,
        y: room_y,
        width: room_width,
        height: room_height
      }
    end
    
    # Connect rooms
    rooms.each_cons(2) do |room1, room2|
      x1 = room1[:x] + room1[:width] / 2
      y1 = room1[:y] + room1[:height] / 2
      x2 = room2[:x] + room2[:width] / 2
      y2 = room2[:y] + room2[:height] / 2
      
      # Create corridor
      x1.upto(x2) { |x| dungeon[y1][x] = 0 if y1 < height && x < width }
      y1.upto(y2) { |y| dungeon[y][x2] = 0 if y < height && x2 < width }
    end
    
    {
      width: width,
      height: height,
      grid: dungeon,
      rooms: rooms
    }
  end
  
  def create_chatbot_personality
    # Chatbot personality generator
    personalities = [
      {
        name: "Helpful Ruby",
        traits: ["friendly", "knowledgeable", "patient"],
        responses: [
          "I'd be happy to help you with Ruby!",
          "Let me explain that concept for you.",
          "That's a great question about Ruby programming!"
        ]
      },
      {
        name: "Sarcastic Bot",
        traits: ["witty", "sarcastic", "clever"],
        responses: [
          "Oh, you want to learn Ruby? How... original.",
          "Sure, I can help. It's not like I have anything better to do.",
          "Ruby? Really? Well, I suppose everyone starts somewhere."
        ]
      },
      {
        name: "Enthusiastic Ruby",
        traits: ["energetic", "excited", "passionate"],
        responses: [
          "RUBY?! I LOVE RUBY! It's the BEST LANGUAGE EVER!",
          "Let me tell you ALL ABOUT RUBY! It's AMAZING!",
          "You want to learn Ruby? THAT'S FANTASTIC! Let's get started!"
        ]
      }
    ]
    
    personality = personalities.sample
    
    {
      name: personality[:name],
      personality: personality[:traits].join(", "),
      responses: personality[:responses]
    }
  end
  
  def interactive_showcase
    loop do
      puts "\n🎨 Creative Ruby Showcase Menu"
      puts "============================="
      puts "Choose a demonstration:"
      puts "1. Metaprogramming Magic"
      puts "2. Artistic Generators"
      puts "3. DSL Creation"
      puts "4. Functional Programming"
      puts "5. Concurrent Programming"
      puts "6. AI Algorithms"
      puts "7. Creative Data Structures"
      puts "8. Language Features"
      puts "9. Performance Optimization"
      puts "10. Creative Application"
      puts "11. Random Demo"
      puts "12. Exit"
      
      print "Choose (1-12): "
      choice = gets.chomp.to_i
      
      case choice
      when 1
        demonstrate_metaprogramming_magic
      when 2
        demonstrate_artistic_generators
      when 3
        demonstrate_dsl_creation
      when 4
        demonstrate_functional_programming
      when 5
        demonstrate_concurrent_programming
      when 6
        demonstrate_ai_algorithms
      when 7
        demonstrate_creative_data_structures
      when 8
        demonstrate_language_features
      when 9
        demonstrate_performance_optimization
      when 10
        create_creative_application
      when 11
        random_demo
      when 12
        break
      else
        puts "Invalid choice. Please try again."
      end
      
      puts "\nPress Enter to continue..."
      gets
    end
    
    puts "\n🎨 Thanks for exploring Creative Ruby!"
    puts "==================================="
    puts "Ruby is not just a language - it's a creative tool!"
    puts "Keep exploring and creating amazing things!"
  end
  
  def random_demo
    demos = [
      :metaprogramming_magic,
      :artistic_generators,
      :dsl_creation,
      :functional_programming,
      :concurrent_programming,
      :ai_algorithms,
      :creative_data_structures,
      :language_features,
      :performance_optimization,
      :creative_application
    ]
    
    demo = demos.sample
    puts "\n🎲 Random Demo: #{demo.to_s.gsub('_', ' ').split.map(&:capitalize).join(' ')}"
    puts "=" * 50
    
    send(demo)
  end
end

# Main execution
if __FILE__ == $0
  puts "🎨 Creative Ruby Showcase"
  puts "========================="
  puts "Discover the creative power of Ruby!"
  puts ""
  
  showcase = CreativeRubyShowcase.new
  showcase.start_showcase
end
