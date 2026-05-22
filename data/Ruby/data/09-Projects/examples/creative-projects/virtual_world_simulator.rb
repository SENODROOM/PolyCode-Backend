# Virtual World Simulator - Creates and manages complex virtual worlds
# This system can simulate ecosystems, economies, societies, and entire planets

require 'json'
require 'securerandom'
require 'fileutils'

class VirtualWorldSimulator
  def initialize
    @worlds = []
    @current_world = nil
    @time_scale = 1.0
    @running = false
    @event_log = []
    @statistics = {}
  end
  
  def start_world_engine
    puts "🌍 Virtual World Simulator"
    puts "=========================="
    puts "Welcome to the ultimate virtual world simulation!"
    
    interactive_world_creation
  end
  
  def create_planetary_system(name = nil)
    puts "🪐 Creating Planetary System..."
    
    name ||= generate_planetary_name
    
    world = {
      id: SecureRandom.uuid,
      name: name,
      type: :planetary_system,
      created_at: Time.now,
      age: 0,
      star: generate_star,
      planets: [],
      asteroids: [],
      comets: [],
      physics: {
        gravity_constant: 6.67430e-11,
        time_scale: @time_scale
      },
      climate: {
        solar_activity: rand(0.5..1.5),
        cosmic_radiation: rand(0.8..1.2)
      }
    }
    
    # Generate planets
    num_planets = rand(3..8)
    num_planets.times do |i|
      planet = generate_planet(i, world[:star])
      world[:planets] << planet
    end
    
    # Generate asteroids
    num_asteroids = rand(50..200)
    num_asteroids.times do
      asteroid = generate_asteroid(world[:star])
      world[:asteroids] << asteroid
    end
    
    # Generate comets
    num_comets = rand(1..5)
    num_comets.times do
      comet = generate_comet(world[:star])
      world[:comets] << comet
    end
    
    @worlds << world
    @current_world = world
    
    puts "✅ Planetary System '#{name}' created!"
    puts "   🌟 Star: #{world[:star][:name]} (#{world[:star][:type]})"
    puts "   🪐 Planets: #{world[:planets].length}"
    puts "   ☄️ Asteroids: #{world[:asteroids].length}"
    puts "   🌠 Comets: #{world[:comets].length}"
    
    world
  end
  
  def create_ecosystem(world, planet_index)
    puts "🌿 Creating Ecosystem on Planet #{planet_index + 1}..."
    
    planet = world[:planets][planet_index]
    return nil unless planet
    
    ecosystem = {
      id: SecureRandom.uuid,
      planet_id: planet[:id],
      created_at: Time.now,
      climate: generate_climate(planet),
      terrain: generate_terrain(planet),
      species: [],
      food_chains: [],
      resources: [],
      environmental_factors: {}
    }
    
    # Generate species
    num_species = rand(20..100)
    num_species.times do |i|
      species = generate_species(ecosystem, i)
      ecosystem[:species] << species
    end
    
    # Generate food chains
    generate_food_chains(ecosystem)
    
    # Generate resources
    generate_resources(ecosystem)
    
    # Set initial environmental factors
    ecosystem[:environmental_factors] = {
      temperature: ecosystem[:climate][:average_temperature],
      humidity: ecosystem[:climate][:humidity],
      oxygen_level: rand(0.15..0.35),
      co2_level: rand(0.0003..0.0006),
      radiation: rand(0.8..1.2)
    }
    
    planet[:ecosystem] = ecosystem
    
    puts "✅ Ecosystem created!"
    puts "   🦎 Species: #{ecosystem[:species].length}"
    puts "   🌱 Plants: #{ecosystem[:species].count { |s| s[:type] == :plant }}"
    puts "   🐾 Animals: #{ecosystem[:species].count { |s| s[:type] == :animal }}"
    puts "   🍄 Resources: #{ecosystem[:resources].length}"
    
    ecosystem
  end
  
  def create_civilization(world, planet_index, species_index = nil)
    puts "🏛️ Creating Civilization..."
    
    planet = world[:planets][planet_index]
    return nil unless planet && planet[:ecosystem]
    
    # Select intelligent species or create one
    if species_index
      species = planet[:ecosystem][:species][species_index]
    else
      # Find or create intelligent species
      species = planet[:ecosystem][:species].find { |s| s[:intelligence] >= 7 }
      unless species
        species = generate_intelligent_species(planet[:ecosystem])
        planet[:ecosystem][:species] << species
      end
    end
    
    civilization = {
      id: SecureRandom.uuid,
      name: generate_civilization_name(species),
      species_id: species[:id],
      species: species,
      created_at: Time.now,
      age: 0,
      population: rand(1000..10000),
      technology_level: 1,
      government: generate_government_type,
      economy: generate_economy_type,
      culture: generate_culture_traits,
      cities: [],
      infrastructure: [],
      diplomacy: {},
      research: {},
      resources: {},
      military: {},
      social_structure: {}
    end
    
    # Generate initial cities
    num_cities = rand(3..10)
    num_cities.times do |i|
      city = generate_city(civilization, i)
      civilization[:cities] << city
    end
    
    # Generate infrastructure
    generate_infrastructure(civilization)
    
    # Set initial resources
    civilization[:resources] = {
      food: rand(1000..5000),
      water: rand(2000..10000),
      energy: rand(500..2000),
      minerals: rand(100..1000),
      technology: rand(50..200)
    }
    
    planet[:civilizations] ||= []
    planet[:civilizations] << civilization
    
    puts "✅ Civilization '#{civilization[:name]}' created!"
    puts "   👥 Population: #{civilization[:population]}"
    puts "   🏛️ Government: #{civilization[:government]}"
    puts "   💰 Economy: #{civilization[:economy]}"
    puts "   🏙️ Cities: #{civilization[:cities].length}"
    
    civilization
  end
  
  def simulate_world(duration_in_years = 100)
    puts "⏱️ Simulating World for #{duration_in_years} years..."
    
    return nil unless @current_world
    
    @running = true
    simulation_steps = duration_in_years * 365  # Daily steps
    
    simulation_steps.times do |step|
      simulate_day
      
      # Log significant events
      if step % 365 == 0  # Yearly summary
        year = step / 365
        puts "Year #{year}: #{get_world_summary}"
      end
      
      # Check for major events
      check_major_events(step)
    end
    
    @running = false
    
    puts "✅ Simulation completed!"
    puts "📊 Final Statistics:"
    puts "   🪐 Planets: #{@current_world[:planets].length}"
    
    @current_world[:planets].each_with_index do |planet, i|
      puts "   🪐 Planet #{i + 1}:"
      puts "     🌿 Ecosystem: #{planet[:ecosystem] ? 'Active' : 'None'}"
      puts "     🏛️ Civilizations: #{planet[:civilizations]&.length || 0}"
      
      if planet[:civilizations]
        planet[:civilizations].each do |civ|
          puts "       🏛️ #{civ[:name]}: Pop #{civ[:population]}, Tech #{civ[:technology_level]}"
        end
      end
    end
  end
  
  def analyze_world_statistics
    return nil unless @current_world
    
    puts "📊 World Statistics Analysis"
    puts "=========================="
    
    stats = {
      total_bodies: @current_world[:planets].length + 
                  @current_world[:asteroids].length + 
                  @current_world[:comets].length,
      habitable_planets: @current_world[:planets].count { |p| p[:habitable] },
      total_species: 0,
      total_population: 0,
      total_civilizations: 0,
      average_technology_level: 0,
      biodiversity_index: 0,
      economic_activity: 0
    }
    
    @current_world[:planets].each do |planet|
      if planet[:ecosystem]
        stats[:total_species] += planet[:ecosystem][:species].length
        stats[:biodiversity_index] += calculate_biodiversity(planet[:ecosystem])
      end
      
      if planet[:civilizations]
        planet[:civilizations].each do |civ|
          stats[:total_population] += civ[:population]
          stats[:total_civilizations] += 1
          stats[:average_technology_level] += civ[:technology_level]
          stats[:economic_activity] += calculate_economic_activity(civ)
        end
      end
    end
    
    # Calculate averages
    if stats[:total_civilizations] > 0
      stats[:average_technology_level] /= stats[:total_civilizations]
      stats[:economic_activity] /= stats[:total_civilizations]
    end
    
    if @current_world[:planets].length > 0
      stats[:biodiversity_index] /= @current_world[:planets].length
    end
    
    # Display statistics
    puts "🪐 Celestial Bodies: #{stats[:total_bodies]}"
    puts "🌍 Habitable Planets: #{stats[:habitable_planets]}"
    puts "🦎 Total Species: #{stats[:total_species]}"
    puts "👥 Total Population: #{stats[:total_population].to_s.reverse.gsub(/(\d{3})(?=\d)/, '\\1,')}"
    puts "🏛️ Civilizations: #{stats[:total_civilizations]}"
    puts "🔬 Avg Tech Level: #{stats[:average_technology_level].round(2)}"
    puts "🌿 Biodiversity Index: #{(stats[:biodiversity_index] * 100).round(2)}%"
    puts "💰 Economic Activity: #{(stats[:economic_activity] * 100).round(2)}%"
    
    stats
  end
  
  def export_world_data(filename = nil)
    return nil unless @current_world
    
    filename ||= "world_export_#{@current_world[:name].gsub(/\s+/, '_').downcase}_#{Time.now.to_i}.json"
    
    world_data = {
      export_info: {
        exported_at: Time.now,
        simulator_version: "1.0.0",
        world_age: @current_world[:age]
      },
      world: @current_world
    }
    
    File.write(filename, JSON.pretty_generate(world_data))
    
    puts "✅ World data exported to: #{filename}"
    
    filename
  end
  
  def import_world_data(filename)
    return nil unless File.exist?(filename)
    
    begin
      data = JSON.parse(File.read(filename))
      world = data["world"]
      
      # Convert string keys back to symbols
      world = symbolize_keys(world)
      
      @worlds << world
      @current_world = world
      
      puts "✅ World imported successfully: #{world[:name]}"
      puts "   📅 Original export: #{data["export_info"]["exported_at"]}"
      puts "   ⏰ World age: #{world[:age]} years"
      
      world
    rescue => e
      puts "❌ Error importing world: #{e.message}"
      nil
    end
  end
  
  def create_world_from_template(template_type)
    puts "🎨 Creating World from Template: #{template_type}"
    
    case template_type
    when :earth_like
      create_earth_like_world
    when :alien_world
      create_alien_world
    when :post_apocalyptic
      create_post_apocalyptic_world
    when :utopian
      create_utopian_world
    when :dystopian
      create_dystopian_world
    when :desert_planet
      create_desert_planet
    when :ocean_world
      create_ocean_world
    when :ice_planet
      create_ice_planet
    else
      puts "Unknown template type: #{template_type}"
      nil
    end
  end
  
  def run_evolution_simulation(generations = 1000)
    puts "🧬 Running Evolution Simulation (#{generations} generations)..."
    
    return nil unless @current_world
    
    evolution_log = []
    
    generations.times do |gen|
      @current_world[:planets].each do |planet|
        next unless planet[:ecosystem]
        
        # Simulate evolution
        evolution_events = simulate_evolution_step(planet[:ecosystem], gen)
        evolution_log.concat(evolution_events)
        
        # Log major evolutionary events
        evolution_events.each do |event|
          if event[:significance] == :major
            puts "🧬 Generation #{gen}: #{event[:description]}"
          end
        end
      end
      
      # Progress indicator
      if (gen + 1) % 100 == 0
        puts "🧬 Generation #{gen + 1}/#{generations} completed"
      end
    end
    
    puts "✅ Evolution simulation completed!"
    puts "📊 Evolution Summary:"
    puts "   🦎 New Species: #{evolution_log.count { |e| e[:type] == :speciation }}"
    puts "   🦴 Extinctions: #{evolution_log.count { |e| e[:type] == :extinction }}"
    puts "   🧬 Adaptations: #{evolution_log.count { |e| e[:type] == :adaptation }}"
    
    evolution_log
  end
  
  def simulate_climate_change(scenario = :moderate)
    puts "🌡️ Simulating Climate Change (#{scenario})..."
    
    return nil unless @current_world
    
    climate_change_log = []
    
    # Define climate change scenarios
    scenarios = {
      mild: { temp_change: 0.5, sea_level_change: 0.1, duration: 50 },
      moderate: { temp_change: 1.5, sea_level_change: 0.5, duration: 100 },
      severe: { temp_change: 3.0, sea_level_change: 2.0, duration: 200 },
      catastrophic: { temp_change: 5.0, sea_level_change: 5.0, duration: 500 }
    }
    
    scenario_data = scenarios[scenario] || scenarios[:moderate]
    
    scenario_data[:duration].times do |year|
      @current_world[:planets].each do |planet|
        next unless planet[:ecosystem]
        
        # Apply climate changes
        climate_impacts = apply_climate_change(planet[:ecosystem], scenario_data, year)
        climate_change_log.concat(climate_impacts)
        
        # Check for ecosystem collapse
        if check_ecosystem_collapse(planet[:ecosystem])
          climate_change_log << {
            year: year,
            planet: planet[:name],
            event: "Ecosystem Collapse",
            description: "The ecosystem on #{planet[:name]} has collapsed due to climate change"
          }
        end
      end
      
      # Log major events
      if (year + 1) % 50 == 0
        puts "🌡️ Year #{year + 1}: Temperature +#{(scenario_data[:temp_change] * year / scenario_data[:duration]).round(2)}°C"
      end
    end
    
    puts "✅ Climate change simulation completed!"
    puts "📊 Climate Change Summary:"
    puts "   🌍 Affected Planets: #{@current_world[:planets].count { |p| p[:ecosystem] }}"
    puts "   🦟 Species Lost: #{climate_change_log.count { |e| e[:event] == "Species Extinction" }}"
    puts "   🏛️ Civilization Impacts: #{climate_change_log.count { |e| e[:event].include?("Civilization") }}"
    
    climate_change_log
  end
  
  def simulate_interstellar_travel
    puts "🚀 Simulating Interstellar Travel..."
    
    return nil unless @current_world
    
    # Check if any civilization has reached space age
    space_faring_civilizations = []
    
    @current_world[:planets].each do |planet|
      next unless planet[:civilizations]
      
      planet[:civilizations].each do |civ|
        if civ[:technology_level] >= 8
          space_faring_civilizations << {
            civilization: civ,
            planet: planet,
            technology: civ[:technology_level]
          }
        end
      end
    end
    
    if space_faring_civilizations.empty?
      puts "No civilizations have reached space age yet."
      return nil
    end
    
    puts "🚀 Found #{space_faring_civilizations.length} space-faring civilizations"
    
    # Simulate space exploration
    exploration_log = []
    
    space_faring_civilizations.each do |sf_civ|
      civ = sf_civ[:civilization]
      origin_planet = sf_civ[:planet]
      
      # Explore other planets
      @current_world[:planets].each do |target_planet|
        next if target_planet == origin_planet
        
        # Calculate travel time and success probability
        distance = calculate_distance(origin_planet, target_planet)
        travel_time = calculate_travel_time(distance, civ[:technology_level])
        success_probability = calculate_mission_success(civ[:technology_level], distance)
        
        # Simulate mission
        if rand < success_probability
          mission_result = {
            civilization: civ[:name],
            origin: origin_planet[:name],
            destination: target_planet[:name],
            distance: distance,
            travel_time: travel_time,
            success: true,
            discoveries: generate_discoveries(target_planet, civ),
            year: @current_world[:age]
          }
          
          # Add diplomatic relations
          if target_planet[:civilizations]
            target_planet[:civilizations].each do |target_civ|
              establish_diplomatic_relations(civ, target_civ)
            end
          end
          
          exploration_log << mission_result
          
          puts "🚀 #{civ[:name]} successfully reached #{target_planet[:name]}!"
        else
          mission_result = {
            civilization: civ[:name],
            origin: origin_planet[:name],
            destination: target_planet[:name],
            distance: distance,
            travel_time: travel_time,
            success: false,
            reason: "Mission failed",
            year: @current_world[:age]
          }
          
          exploration_log << mission_result
        end
      end
    end
    
    puts "✅ Interstellar travel simulation completed!"
    puts "📊 Exploration Summary:"
    puts "   🚀 Successful Missions: #{exploration_log.count { |m| m[:success] }}"
    puts "   ❌ Failed Missions: #{exploration_log.count { |m| !m[:success] }}"
    puts "   🌍 Planets Contacted: #{exploration_log.select { |m| m[:success] }.map { |m| m[:destination] }.uniq.length}"
    
    exploration_log
  end
  
  private
  
  def generate_planetary_name
    prefixes = ["Alpha", "Beta", "Gamma", "Delta", "Epsilon", "Zeta", "Eta", "Theta"]
    suffixes = ["Centauri", "Orionis", "Majoris", "Minoris", "Prime", "Secundus", "Tertius", "Quartus"]
    numbers = ["I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X"]
    
    "#{prefixes.sample} #{suffixes.sample} #{numbers.sample}"
  end
  
  def generate_star
    star_types = [
      { type: :red_dwarf, temperature: 3000, mass: 0.1, luminosity: 0.001, color: "#FF6B6B" },
      { type: :yellow_dwarf, temperature: 6000, mass: 1.0, luminosity: 1.0, color: "#FFD93D" },
      { type: :blue_giant, temperature: 10000, mass: 10.0, luminosity: 100, color: "#6BCB77" },
      { type: :red_giant, temperature: 4000, mass: 5.0, luminosity: 10, color: "#FF6B6B" },
      { type: :white_dwarf, temperature: 8000, mass: 0.5, luminosity: 0.01, color: "#FFFFFF" }
    ]
    
    star_data = star_types.sample
    
    {
      name: generate_star_name,
      type: star_data[:type],
      temperature: star_data[:temperature],
      mass: star_data[:mass],
      luminosity: star_data[:luminosity],
      color: star_data[:color],
      age: rand(1e9..1e10),
      radius: calculate_star_radius(star_data[:mass])
    }
  end
  
  def generate_star_name
    star_names = ["Sol", "Alpha", "Beta", "Gamma", "Delta", "Epsilon", "Zeta", "Eta"]
    star_names.sample
  end
  
  def calculate_star_radius(mass)
    # Simplified stellar radius calculation
    0.1 * (mass ** 0.8)
  end
  
  def generate_planet(index, star)
    planet_types = [
      { type: :rocky, habitable: true, atmosphere: true, water: true },
      { type: :gas_giant, habitable: false, atmosphere: true, water: false },
      { type: :ice_giant, habitable: false, atmosphere: true, water: false },
      { type: :desert, habitable: false, atmosphere: false, water: false },
      { type: :ocean, habitable: true, atmosphere: true, water: true },
      { type: :forest, habitable: true, atmosphere: true, water: true }
    ]
    
    planet_data = planet_types.sample
    
    # Calculate orbital parameters
    orbital_radius = 0.3 + (index * 0.4) + rand(-0.1..0.1)
    orbital_period = calculate_orbital_period(orbital_radius, star[:mass])
    
    {
      id: SecureRandom.uuid,
      name: generate_planet_name(index),
      type: planet_data[:type],
      habitable: planet_data[:habitable],
      atmosphere: planet_data[:atmosphere],
      water: planet_data[:water],
      orbital_radius: orbital_radius,
      orbital_period: orbital_period,
      radius: calculate_planet_radius(planet_data[:type]),
      mass: calculate_planet_mass(planet_data[:type]),
      temperature: calculate_planet_temperature(orbital_radius, star[:temperature]),
      gravity: calculate_planet_gravity(planet_data[:type]),
      moons: generate_moons(rand(0..3)),
      day_length: calculate_day_length(orbital_radius),
      year_length: orbital_period,
      axial_tilt: rand(0..45),
      eccentricity: rand(0..0.3)
    }
  end
  
  def generate_planet_name(index)
    names = ["Mercury", "Venus", "Earth", "Mars", "Jupiter", "Saturn", "Uranus", "Neptune"]
    names[index % names.length]
  end
  
  def calculate_orbital_period(radius, star_mass)
    # Kepler's third law: T² ∝ a³/M
    # Simplified calculation
    365.25 * Math.sqrt((radius ** 3) / star_mass)
  end
  
  def calculate_planet_radius(type)
    radii = {
      rocky: rand(0.5..2.0),
      gas_giant: rand(5.0..15.0),
      ice_giant: rand(3.0..8.0),
      desert: rand(0.8..1.5),
      ocean: rand(0.9..1.8),
      forest: rand(0.8..1.6)
    }
    
    radii[type] || 1.0
  end
  
  def calculate_planet_mass(type)
    masses = {
      rocky: rand(0.1..2.0),
      gas_giant: rand(50.0..300.0),
      ice_giant: rand(10.0::50.0),
      desert: rand(0.3..1.0),
      ocean: rand(0.5..2.0),
      forest: rand(0.4..1.8)
    }
    
    masses[type] || 1.0
  end
  
  def calculate_planet_temperature(orbital_radius, star_temperature)
    # Simplified temperature calculation
    # T = T_star * sqrt(R_star / (2 * orbital_radius))
    star_temperature * Math.sqrt(1.0 / (2 * orbital_radius))
  end
  
  def calculate_planet_gravity(type)
    gravities = {
      rocky: rand(0.8..1.2),
      gas_giant: rand(2.0..5.0),
      ice_giant: rand(1.0..2.5),
      desert: rand(0.6..1.0),
      ocean: rand(0.9..1.1),
      forest: rand(0.8..1.2)
    }
    
    gravities[type] || 1.0
  end
  
  def calculate_day_length(orbital_radius)
    # Simplified day length calculation
    24.0 * (1 + rand(-0.5..0.5))
  end
  
  def generate_moons(num_moons)
    moons = []
    
    num_moons.times do |i|
      moons << {
        id: SecureRandom.uuid,
        name: "Moon #{i + 1}",
        radius: rand(0.1..0.5),
        orbital_radius: 0.01 + (i * 0.02),
        orbital_period: 10 + (i * 5)
      }
    end
    
    moons
  end
  
  def generate_asteroid(star)
    {
      id: SecureRandom.uuid,
      name: "Asteroid-#{SecureRandom.hex(4)}",
      type: :asteroid,
      radius: rand(0.001..0.01),
      orbital_radius: rand(2.0..10.0),
      orbital_period: rand(1000..5000),
      composition: [:rock, :metal, :ice].sample,
      mass: rand(1e15..1e18)
    }
  end
  
  def generate_comet(star)
    {
      id: SecureRandom.uuid,
      name: "Comet-#{SecureRandom.hex(4)}",
      type: :comet,
      radius: rand(0.01..0.1),
      orbital_radius: rand(5.0..50.0),
      orbital_period: rand(10000..100000),
      eccentricity: rand(0.7..0.99),
      composition: [:ice, :dust, :rock].sample,
      mass: rand(1e13..1e16)
    }
  end
  
  def generate_climate(planet)
    climate_types = {
      rocky: {
        average_temperature: planet[:temperature],
        humidity: rand(0.3..0.7),
        precipitation: rand(100..1000),
        wind_speed: rand(5..30),
        seasons: planet[:axial_tilt] > 20
      },
      ocean: {
        average_temperature: planet[:temperature],
        humidity: rand(0.7..1.0),
        precipitation: rand(500..2000),
        wind_speed: rand(10..40),
        seasons: planet[:axial_tilt] > 15
      },
      forest: {
        average_temperature: planet[:temperature],
        humidity: rand(0.6..0.9),
        precipitation: rand(800..1500),
        wind_speed: rand(3..20),
        seasons: planet[:axial_tilt] > 10
      },
      desert: {
        average_temperature: planet[:temperature],
        humidity: rand(0.1..0.3),
        precipitation: rand(10..100),
        wind_speed: rand(10..50),
        seasons: planet[:axial_tilt] > 30
      }
    }
    
    climate_types[planet[:type]] || climate_types[:rocky]
  end
  
  def generate_terrain(planet)
    terrain_types = {
      rocky: {
        mountains: rand(20..40),
        plains: rand(20..40),
        hills: rand(10..30),
        valleys: rand(5..15),
        caves: rand(5..15)
      },
      ocean: {
        ocean: rand(70..90),
        islands: rand(10..30),
        reefs: rand(5..15),
        trenches: rand(5..15)
      },
      forest: {
        forest: rand(40..60),
        plains: rand(20..30),
        mountains: rand(10..20),
        rivers: rand(5..15),
        lakes: rand(5..15)
      },
      desert: {
        desert: rand(60..80),
        dunes: rand(10..20),
        rock_formations: rand(5..15),
        oases: rand(1..5)
      }
    }
    
    terrain_types[planet[:type]] || terrain_types[:rocky]
  end
  
  def generate_species(ecosystem, index)
    species_types = [:plant, :herbivore, :carnivore, :omnivore, :decomposer]
    
    species_type = species_types.sample
    
    species = {
      id: SecureRandom.uuid,
      name: generate_species_name(species_type, index),
      type: species_type,
      population: rand(100..10000),
      size: rand(0.1..10.0),
      lifespan: rand(1..100),
      reproduction_rate: rand(0.1..5.0),
      diet: generate_diet(species_type),
      habitat: generate_habitat(ecosystem),
      adaptations: generate_adaptations(species_type, ecosystem),
      intelligence: rand(1..10),
      social_structure: generate_social_structure(species_type),
      evolution_stage: :stable,
      genetic_diversity: rand(0.5..1.0)
    }
    
    species
  end
  
  def generate_species_name(type, index)
    prefixes = {
      plant: ["Flora", "Verdant", "Chloro", "Photosyn", "Botano"],
      herbivore: ["Herbi", "Vegi", "Planta", "Folli", "Graze"],
      carnivore: ["Carni", "Preda", "Fang", "Claw", "Hunt"],
      omnivore: ["Omni", "Flexi", "Adapt", "Versa", "Multi"],
      decomposer: ["Decom", "Fungi", "Mold", "Rot", "Recyc"]
    }
    
    suffixes = ["saurus", "don", "rex", "morph", "form", "type", "kind", "species"]
    
    "#{prefixes[type].sample}#{suffixes.sample}#{index}"
  end
  
  def generate_diet(type)
    diets = {
      plant: ["photosynthesis", "soil_nutrients", "water"],
      herbivore: ["leaves", "fruits", "roots", "grass"],
      carnivore: ["meat", "fish", "insects", "small_animals"],
      omnivore: ["plants", "animals", "insects", "fruits"],
      decomposer: ["dead_organic_matter", "waste", "soil_nutrients"]
    }
    
    diets[type] || diets[:omnivore]
  end
  
  def generate_habitat(ecosystem)
    habitats = []
    
    ecosystem[:terrain].each do |terrain_type, percentage|
      next unless percentage > 0
      
      habitats << {
        type: terrain_type,
        preference: rand(0.5..1.0),
        population_density: rand(0.1..1.0)
      }
    end
    
    habitats
  end
  
  def generate_adaptations(type, ecosystem)
    adaptations = []
    
    # Climate adaptations
    if ecosystem[:climate][:average_temperature] < 0
      adaptations << "cold_resistance"
    elsif ecosystem[:climate][:average_temperature] > 30
      adaptations << "heat_resistance"
    end
    
    # Terrain adaptations
    if ecosystem[:terrain][:mountains] > 30
      adaptations << "climbing_ability"
    end
    
    if ecosystem[:terrain][:ocean] > 50
      adaptations << "swimming_ability"
    end
    
    # Type-specific adaptations
    case type
    when :plant
      adaptations.concat(["photosynthesis", "root_system", "reproduction"])
    when :herbivore
      adaptations.concat(["digestive_system", "speed", "camouflage"])
    when :carnivore
      adaptations.concat(["hunting_ability", "strength", "senses"])
    when :omnivore
      adaptations.concat(["versatile_diet", "adaptability", "intelligence"])
    when :decomposer
      adaptations.concat(["decomposition", "recycling", "efficiency"])
    end
    
    adaptations.sample(rand(2..5))
  end
  
  def generate_social_structure(type)
    structures = {
      plant: ["solitary", "colonial", "networked"],
      herbivore: ["solitary", "herd", "family_group"],
      carnivore: ["solitary", "pack", "pride"],
      omnivore: ["solitary", "family_group", "tribal"],
      decomposer: ["solitary", "colonial", "networked"]
    }
    
    structures[type].sample
  end
  
  def generate_intelligent_species(ecosystem)
    # Create a species with high intelligence
    species = generate_species(ecosystem, ecosystem[:species].length)
    species[:intelligence] = rand(7..10)
    species[:name] = generate_species_name(:intelligent, ecosystem[:species].length)
    species[:type] = :intelligent
    species[:tool_use] = true
    species[:language] = true
    species[:culture] = generate_culture_traits
    
    species
  end
  
  def generate_food_chains(ecosystem)
    # Create food chains based on species types
    food_chains = []
    
    plants = ecosystem[:species].select { |s| s[:type] == :plant }
    herbivores = ecosystem[:species].select { |s| s[:type] == :herbivore }
    carnivores = ecosystem[:species].select { |s| s[:type] == :carnivore }
    omnivores = ecosystem[:species].select { |s| s[:type] == :omnivore }
    decomposers = ecosystem[:species].select { |s| s[:type] == :decomposer }
    
    # Create basic food chains
    plants.each do |plant|
      herbivores.sample(2).each do |herbivore|
        food_chains << {
          producer: plant,
          primary_consumer: herbivore,
          secondary_consumers: carnivores.sample(1),
          decomposers: decomposers.sample(1)
        }
      end
    end
    
    food_chains
  end
  
  def generate_resources(ecosystem)
    resources = []
    
    # Generate natural resources
    resource_types = [
      { type: :water, abundance: rand(0.5..1.0), renewable: true },
      { type: :minerals, abundance: rand(0.1..0.8), renewable: false },
      { type: :organic_matter, abundance: rand(0.3..0.9), renewable: true },
      { type: :energy_sources, abundance: rand(0.2..0.7), renewable: true },
      { type: :building_materials, abundance: rand(0.4..0.8), renewable: false }
    ]
    
    resource_types.each do |resource|
      resources << resource.merge({
        id: SecureRandom.uuid,
        location: generate_resource_location(ecosystem),
        accessibility: rand(0.5..1.0)
      })
    end
    
    resources
  end
  
  def generate_resource_location(ecosystem)
    # Generate a location within the ecosystem
    terrain_types = ecosystem[:terrain].keys
    
    {
      terrain: terrain_types.sample,
      coordinates: {
        x: rand(0..100),
        y: rand(0..100)
      },
      depth: rand(0..100)
    }
  end
  
  def calculate_biodiversity(ecosystem)
    # Calculate biodiversity index
    species_count = ecosystem[:species].length
    genetic_diversity = ecosystem[:species].map { |s| s[:genetic_diversity] }.sum / species_count
    
    # Shannon diversity index (simplified)
    diversity = 0
    ecosystem[:species].each do |species|
      proportion = species[:population].to_f / ecosystem[:species].sum { |s| s[:population] }
      diversity -= proportion * Math.log(proportion) if proportion > 0
    end
    
    (diversity / Math.log(species_count)) * genetic_diversity
  end
  
  def generate_civilization_name(species)
    prefixes = ["New", "Grand", "United", "Free", "Advanced", "Ancient", "Future", "Stellar"]
    suffixes = ["Nation", "Republic", "Empire", "Federation", "Alliance", "Union", "Dominion", "Collective"]
    
    "#{prefixes.sample} #{species[:name].capitalize} #{suffixes.sample}"
  end
  
  def generate_government_type
    governments = [
      :democracy, :republic, :monarchy, :dictatorship, :theocracy,
      :anarchy, :technocracy, :meritocracy, :corporatocracy, :feudal
    ]
    
    governments.sample
  end
  
  def generate_economy_type
    economies = [
      :capitalist, :socialist, :mixed, :traditional, :command,
      :market, :planned, :barter, :gift, :sharing
    ]
    
    economies.sample
  end
  
  def generate_culture_traits
    traits = [
      "individualistic", "collectivist", "traditional", "progressive",
      "religious", "secular", "militaristic", "pacifist",
      "expansionist", "isolationist", "innovative", "conservative"
    ]
    
    traits.sample(rand(3..6))
  end
  
  def generate_city(civilization, index)
    {
      id: SecureRandom.uuid,
      name: "#{civilization[:name]} City #{index + 1}",
      population: rand(1000..100000),
      size: rand(1..10),
      infrastructure_level: rand(1..10),
      specialization: generate_city_specialization,
      location: generate_city_location(civilization),
      founded: civilization[:age] - rand(0..100),
      prosperity: rand(0.3..1.0)
    }
  end
  
  def generate_city_specialization
    specializations = [
      :capital, :industrial, :agricultural, :commercial, :military,
      :research, :cultural, :port, :mining, :religious
    ]
    
    specializations.sample
  end
  
  def generate_city_location(civilization)
    # Generate city location relative to other cities
    {
      x: rand(0..100),
      y: rand(0..100),
      terrain: generate_terrain_type,
      resources: rand(1..10)
    }
  end
  
  def generate_terrain_type
    terrain_types = [:plains, :mountains, :coastal, :forest, :desert, :river]
    terrain_types.sample
  end
  
  def generate_infrastructure(civilization)
    infrastructure = {
      transportation: rand(1..10),
      communication: rand(1..10),
      energy: rand(1..10),
      water: rand(1..10),
      waste_management: rand(1..10),
      education: rand(1..10),
      healthcare: rand(1..10),
      defense: rand(1..10)
    }
    
    infrastructure
  end
  
  def calculate_economic_activity(civilization)
    # Calculate economic activity based on population, technology, and infrastructure
    base_activity = civilization[:population] / 1000.0
    tech_multiplier = civilization[:technology_level] / 10.0
    infrastructure_multiplier = civilization[:infrastructure].values.sum / 80.0
    
    base_activity * tech_multiplier * infrastructure_multiplier
  end
  
  def simulate_day
    @current_world[:age] += 1 / 365.0
    
    # Simulate each planet
    @current_world[:planets].each do |planet|
      simulate_planet(planet)
    end
    
    # Simulate celestial mechanics
    simulate_celestial_mechanics
    
    # Check for random events
    check_random_events
  end
  
  def simulate_planet(planet)
    # Simulate ecosystem
    if planet[:ecosystem]
      simulate_ecosystem(planet[:ecosystem])
    end
    
    # Simulate civilizations
    if planet[:civilizations]
      planet[:civilizations].each do |civilization|
        simulate_civilization(civilization)
      end
    end
    
    # Simulate climate
    simulate_climate(planet)
  end
  
  def simulate_ecosystem(ecosystem)
    # Simulate species populations
    ecosystem[:species].each do |species|
      # Population dynamics
      growth = species[:reproduction_rate] * species[:population] * 0.01
      death = species[:population] * 0.001 * (10 - species[:lifespan]) / 10
      
      species[:population] += (growth - death).round
      species[:population] = [species[:population], 1].max
    end
    
    # Simulate food chains
    ecosystem[:food_chains].each do |food_chain|
      simulate_food_chain(food_chain, ecosystem)
    end
    
    # Check for extinctions
    check_extinctions(ecosystem)
    
    # Check for speciation
    check_speciation(ecosystem)
  end
  
  def simulate_food_chain(food_chain, ecosystem)
    # Simplified food chain simulation
    producer = food_chain[:producer]
    primary_consumer = food_chain[:primary_consumer]
    
    # Energy transfer
    energy_transfer = 0.1
    
    # Update populations based on food availability
    if primary_consumer[:population] > 0
      consumption = [primary_consumer[:population] * 0.01, producer[:population] * 0.1].min
      producer[:population] -= consumption.round
      producer[:population] = [producer[:population], 0].max
    end
  end
  
  def check_extinctions(ecosystem)
    extinct_species = []
    
    ecosystem[:species].each do |species|
      if species[:population] <= 0
        extinct_species << species
      end
    end
    
    extinct_species.each do |species|
      ecosystem[:species].delete(species)
      @event_log << {
        year: @current_world[:age],
        event: "Species Extinction",
        description: "#{species[:name]} has gone extinct"
      }
    end
  end
  
  def check_speciation(ecosystem)
    # Check for new species formation
    if rand < 0.001 && ecosystem[:species].length < 200
      parent_species = ecosystem[:species].sample
      new_species = parent_species.dup
      
      new_species[:id] = SecureRandom.uuid
      new_species[:name] = generate_species_name(new_species[:type], ecosystem[:species].length)
      new_species[:population] = rand(10..100)
      new_species[:genetic_diversity] = rand(0.8..1.0)
      new_species[:adaptations] = generate_adaptations(new_species[:type], ecosystem)
      
      ecosystem[:species] << new_species
      
      @event_log << {
        year: @current_world[:age],
        event: "Speciation",
        description: "New species #{new_species[:name]} has evolved"
      }
    end
  end
  
  def simulate_civilization(civilization)
    # Population growth
    growth_rate = 0.02 * (1 - civilization[:population] / 1000000.0)
    civilization[:population] += (civilization[:population] * growth_rate).round
    
    # Technology advancement
    if rand < 0.01
      civilization[:technology_level] += 1
      @event_log << {
        year: @current_world[:age],
        event: "Technological Advancement",
        civilization: civilization[:name],
        description: "#{civilization[:name]} has reached technology level #{civilization[:technology_level]}"
      }
    end
    
    # Resource management
    civilization[:resources][:food] -= civilization[:population] * 0.001
    civilization[:resources][:water] -= civilization[:population] * 0.002
    civilization[:resources][:energy] -= civilization[:population] * 0.0005
    
    # Check for resource shortages
    check_resource_shortages(civilization)
    
    # Update cities
    civilization[:cities].each do |city|
      simulate_city(city, civilization)
    end
  end
  
  def check_resource_shortages(civilization)
    shortages = []
    
    civilization[:resources].each do |resource, amount|
      if amount <= 0
        shortages << resource
      end
    end
    
    if shortages.any?
      # Handle shortages
      handle_resource_shortages(civilization, shortages)
    end
  end
  
  def handle_resource_shortages(civilization, shortages)
    # Reduce population due to shortages
    population_loss = civilization[:population] * 0.05 * shortages.length
    civilization[:population] -= population_loss.round
    
    @event_log << {
      year: @current_world[:age],
      event: "Resource Shortage",
      civilization: civilization[:name],
      description: "#{civilization[:name]} is experiencing shortages in #{shortages.join(', ')}"
    }
  end
  
  def simulate_city(city, civilization)
    # City growth
    if city[:prosperity] > 0.7
      city[:population] += rand(10..100)
    elsif city[:prosperity] < 0.3
      city[:population] -= rand(5..50)
    end
    
    # Update prosperity
    city[:prosperity] += rand(-0.1..0.1)
    city[:prosperity] = [[0, city[:prosperity]].max, 1.0].min
  end
  
  def simulate_climate(planet)
    # Simplified climate simulation
    planet[:ecosystem][:climate][:temperature] += rand(-0.5..0.5)
    planet[:ecosystem][:climate][:humidity] += rand(-0.05..0.05)
    
    # Keep values in reasonable ranges
    planet[:ecosystem][:climate][:temperature] = [[-50, planet[:ecosystem][:climate][:temperature]].max, 100].min
    planet[:ecosystem][:climate][:humidity] = [[0, planet[:ecosystem][:climate][:humidity]].max, 1.0].min
  end
  
  def simulate_celestial_mechanics
    # Update orbital positions
    @current_world[:planets].each do |planet|
      angle = (2 * Math::PI * @current_world[:age]) / planet[:orbital_period]
      
      # Update position (simplified)
      planet[:x] = planet[:orbital_radius] * Math.cos(angle)
      planet[:y] = planet[:orbital_radius] * Math.sin(angle)
    end
  end
  
  def check_random_events
    # Random events with low probability
    if rand < 0.001
      event = generate_random_event
      @event_log << event
      
      puts "🌟 Random Event: #{event[:description]}"
    end
  end
  
  def generate_random_event
    events = [
      {
        type: :solar_flare,
        description: "Solar flare affects planetary communications"
      },
      {
        type: :asteroid_impact,
        description: "Small asteroid impacts a planet"
      },
      {
        type: :magnetic_storm,
        description: "Magnetic storm disrupts technology"
      },
      {
        type: :volcanic_eruption,
        description: "Volcanic eruption affects climate"
      }
    ]
    
    events.sample.merge(year: @current_world[:age])
  end
  
  def check_major_events(step)
    # Check for civilization-level events
    @current_world[:planets].each do |planet|
      next unless planet[:civilizations]
      
      planet[:civilizations].each do |civ|
        if civ[:technology_level] == 10 && !civ[:space_age]
          civ[:space_age] = true
          @event_log << {
            year: @current_world[:age],
            event: "Space Age",
            civilization: civ[:name],
            description: "#{civ[:name]} has entered the space age!"
          }
        end
      end
    end
  end
  
  def get_world_summary
    summary = []
    
    @current_world[:planets].each_with_index do |planet, i|
      planet_info = "Planet #{i + 1}: "
      
      if planet[:ecosystem]
        planet_info += "#{planet[:ecosystem][:species].length} species"
      else
        planet_info += "No ecosystem"
      end
      
      if planet[:civilizations]
        planet_info += ", #{planet[:civilizations].length} civilizations"
      end
      
      summary << planet_info
    end
    
    summary.join("; ")
  end
  
  def check_ecosystem_collapse(ecosystem)
    # Check if ecosystem has collapsed
    species_count = ecosystem[:species].length
    total_population = ecosystem[:species].sum { |s| s[:population] }
    
    species_count < 5 || total_population < 100
  end
  
  def apply_climate_change(ecosystem, scenario_data, year)
    impacts = []
    
    # Apply temperature change
    temp_change = scenario_data[:temp_change] * year / scenario_data[:duration]
    ecosystem[:climate][:average_temperature] += temp_change
    
    # Apply sea level change (affects ocean planets)
    if ecosystem[:terrain][:ocean] > 50
      sea_level_change = scenario_data[:sea_level_change] * year / scenario_data[:duration]
      ecosystem[:terrain][:ocean] += sea_level_change * 10
      ecosystem[:terrain][:islands] -= sea_level_change * 5
    end
    
    # Check for species impacts
    ecosystem[:species].each do |species|
      if species[:population] > 0
        survival_probability = calculate_survival_probability(species, temp_change, sea_level_change)
        
        if rand > survival_probability
          species[:population] = 0
          impacts << {
            type: :species_extinction,
            species: species[:name],
            reason: "Climate change"
          }
        end
      end
    end
    
    impacts
  end
  
  def calculate_survival_probability(species, temp_change, sea_level_change)
    base_probability = 0.8
    
    # Temperature tolerance
    if species[:adaptations].include?("heat_resistance")
      base_probability += 0.1 if temp_change > 0
    else
      base_probability -= 0.1 * (temp_change.abs / 5.0)
    end
    
    if species[:adaptations].include?("cold_resistance")
      base_probability += 0.1 if temp_change < 0
    else
      base_probability -= 0.1 * (temp_change.abs / 5.0)
    end
    
    # Sea level tolerance
    if species[:adaptations].include?("swimming_ability")
      base_probability += 0.1 * (1 - sea_level_change.abs / 5.0)
    else
      base_probability -= 0.1 * (sea_level_change.abs / 5.0)
    end
    
    [[0, base_probability].max, 1.0].min
  end
  
  def calculate_distance(planet1, planet2)
    # Simplified distance calculation
    Math.sqrt((planet1[:x] - planet2[:x]) ** 2 + (planet1[:y] - planet2[:y]) ** 2)
  end
  
  def calculate_travel_time(distance, technology_level)
    # Travel time in years (simplified)
    base_speed = technology_level * 0.1
    distance / base_speed
  end
  
  def calculate_mission_success(technology_level, distance)
    # Success probability based on technology and distance
    base_success = technology_level / 10.0
    distance_penalty = distance / 100.0
    
    [[0, base_success - distance_penalty].max, 1.0].min
  end
  
  def generate_discoveries(planet, civilization)
    discoveries = []
    
    if planet[:ecosystem]
      # Discover species
      planet[:ecosystem][:species].sample(3).each do |species|
        discoveries << {
          type: :species,
          name: species[:name],
          description: "Discovered #{species[:name]} on #{planet[:name]}"
        }
      end
      
      # Discover resources
      planet[:ecosystem][:resources].sample(2).each do |resource|
        discoveries << {
          type: :resource,
          name: resource[:type],
          description: "Discovered #{resource[:type]} on #{planet[:name]}"
        }
      end
    end
    
    discoveries
  end
  
  def establish_diplomatic_relations(civ1, civ2)
    civ1[:diplomacy][c2[:id]] = {
      status: :neutral,
      trade: false,
      war: false,
      alliance: false
    }
    
    civ2[:diplomacy][civ1[:id]] = {
      status: :neutral,
      trade: false,
      war: false,
      alliance: false
    }
  end
  
  def simulate_evolution_step(ecosystem, generation)
    events = []
    
    # Natural selection
    ecosystem[:species].each do |species|
      if species[:population] > 0
        # Calculate fitness
        fitness = calculate_species_fitness(species, ecosystem)
        
        # Evolutionary pressure
        if rand < (1 - fitness) * 0.01
          # Adapt or die
          if rand < 0.5
            # Adaptation
            new_adaptation = generate_evolutionary_adaptation(species, ecosystem)
            species[:adaptations] << new_adaptation
            
            events << {
              type: :adaptation,
              species: species[:name],
              adaptation: new_adaptation,
              generation: generation,
              significance: :minor
            }
          else
            # Population reduction
            species[:population] = (species[:population] * 0.8).round
            
            events << {
              type: :selection_pressure,
              species: species[:name],
              population_change: -20,
              generation: generation,
              significance: :minor
            }
          end
        end
      end
    end
    
    # Speciation
    if rand < 0.001 && ecosystem[:species].length < 200
      parent_species = ecosystem[:species].select { |s| s[:population] > 100 }.sample
      
      if parent_species
        new_species = create_new_species(parent_species, ecosystem)
        ecosystem[:species] << new_species
        
        events << {
          type: :speciation,
          parent: parent_species[:name],
          new_species: new_species[:name],
          generation: generation,
          significance: :major
        }
      end
    end
    
    # Extinction
    ecosystem[:species].each do |species|
      if species[:population] <= 0
        ecosystem[:species].delete(species)
        
        events << {
          type: :extinction,
          species: species[:name],
          generation: generation,
          significance: :major
        }
      end
    end
    
    events
  end
  
  def calculate_species_fitness(species, ecosystem)
    # Calculate fitness based on adaptations and environment
    fitness = 0.5
    
    # Climate fitness
    climate_temp = ecosystem[:climate][:average_temperature]
    ideal_temp = 20  # Ideal temperature for most species
    
    temp_fitness = 1 - (climate_temp - ideal_temp).abs / 50
    fitness += temp_fitness * 0.3
    
    # Habitat fitness
    habitat_fitness = species[:habitat].map { |h| h[:preference] }.sum / species[:habitat].length
    fitness += habitat_fitness * 0.3
    
    # Adaptation fitness
    adaptation_fitness = species[:adaptations].length * 0.1
    fitness += adaptation_fitness
    
    [[0, fitness].max, 1.0].min
  end
  
  def generate_evolutionary_adaptation(species, ecosystem)
    possible_adaptations = [
      "enhanced_senses", "improved_camouflage", "better_digestion",
      "stronger_limbs", "thermal_regulation", "water_conservation",
      "enhanced_reproduction", "social_behavior", "tool_use"
    ]
    
    possible_adaptations.sample
  end
  
  def create_new_species(parent_species, ecosystem)
    new_species = parent_species.dup
    
    new_species[:id] = SecureRandom.uuid
    new_species[:name] = generate_species_name(new_species[:type], ecosystem[:species].length)
    new_species[:population] = rand(10..100)
    new_species[:genetic_diversity] = parent_species[:genetic_diversity] * 0.9
    new_species[:adaptations] = parent_species[:adaptations].sample(rand(2..4))
    
    # Add new adaptation
    new_species[:adaptations] << generate_evolutionary_adaptation(new_species, ecosystem)
    
    new_species
  end
  
  def symbolize_keys(hash)
    # Convert string keys to symbols recursively
    hash.transform_keys do |key|
      key.to_sym
    end
  end
  
  def interactive_world_creation
    puts "\n🌍 Interactive World Creation"
    puts "=========================="
    
    loop do
      puts "\nWhat would you like to do?"
      puts "1. Create new planetary system"
      puts "2. Create world from template"
      puts "3. Add ecosystem to planet"
      puts "4. Create civilization"
      puts "5. Simulate world"
      puts "6. Analyze statistics"
      puts "7. Export world"
      puts "8. Import world"
      puts "9. Run evolution simulation"
      puts "10. Simulate climate change"
      puts "11. Simulate interstellar travel"
      puts "12. View event log"
      puts "13. Exit"
      
      print "Choose an option (1-13): "
      choice = gets.chomp.to_i
      
      case choice
      when 1
        create_planetary_system_interactive
      when 2
        create_world_from_template_interactive
      when 3
        add_ecosystem_interactive
      when 4
        create_civilization_interactive
      when 5
        simulate_world_interactive
      when 6
        analyze_world_statistics
      when 7
        export_world_data
      when 8
        import_world_data_interactive
      when 9
        run_evolution_simulation_interactive
      when 10
        simulate_climate_change_interactive
      when 11
        simulate_interstellar_travel
      when 12
        view_event_log
      when 13
        break
      else
        puts "Invalid choice. Please try again."
      end
    end
  end
  
  def create_planetary_system_interactive
    print "Enter system name (or press Enter for random): "
    name = gets.chomp.strip
    
    create_planetary_system(name.empty? ? nil : name)
  end
  
  def create_world_from_template_interactive
    puts "\nAvailable templates:"
    puts "1. Earth-like"
    puts "2. Alien world"
    puts "3. Post-apocalyptic"
    puts "4. Utopian"
    puts "5. Dystopian"
    puts "6. Desert planet"
    puts "7. Ocean world"
    puts "8. Ice planet"
    
    print "Choose template (1-8): "
    choice = gets.chomp.to_i
    
    templates = [:earth_like, :alien_world, :post_apocalyptic, :utopian, :dystopian, :desert_planet, :ocean_world, :ice_planet]
    template = templates[choice - 1] || :earth_like
    
    create_world_from_template(template)
  end
  
  def add_ecosystem_interactive
    return unless @current_world
    
    puts "\nAvailable planets:"
    @current_world[:planets].each_with_index do |planet, i|
      status = planet[:ecosystem] ? "Has ecosystem" : "No ecosystem"
      puts "#{i + 1}. #{planet[:name]} - #{status}"
    end
    
    print "Choose planet (1-#{@current_world[:planets].length}): "
    choice = gets.chomp.to_i - 1
    
    if choice >= 0 && choice < @current_world[:planets].length
      planet = @current_world[:planets][choice]
      
      if planet[:ecosystem]
        puts "Planet #{planet[:name]} already has an ecosystem."
      else
        create_ecosystem(@current_world, choice)
      end
    else
      puts "Invalid choice."
    end
  end
  
  def create_civilization_interactive
    return unless @current_world
    
    puts "\nAvailable planets with ecosystems:"
    available_planets = []
    
    @current_world[:planets].each_with_index do |planet, i|
      if planet[:ecosystem]
        available_planets << planet
        puts "#{i + 1}. #{planet[:name]} - #{planet[:ecosystem][:species].length} species"
      end
    end
    
    return if available_planets.empty?
    
    print "Choose planet (1-#{available_planets.length}): "
    choice = gets.chomp.to_i - 1
    
    if choice >= 0 && choice < available_planets.length
      planet = available_planets[choice]
      planet_index = @current_world[:planets].index(planet)
      
      print "Choose species index (or press Enter for auto-select): "
      species_input = gets.chomp.strip
      
      species_index = species_input.empty? ? nil : species_input.to_i
      
      create_civilization(@current_world, planet_index, species_index)
    else
      puts "Invalid choice."
    end
  end
  
  def simulate_world_interactive
    print "Simulation duration in years (default 100): "
    duration = gets.chomp.to_i
    duration = 100 if duration <= 0
    
    simulate_world(duration)
  end
  
  def import_world_data_interactive
    print "Enter filename to import: "
    filename = gets.chomp.strip
    
    if filename.empty?
      puts "No filename provided."
      return
    end
    
    import_world_data(filename)
  end
  
  def run_evolution_simulation_interactive
    print "Number of generations (default 1000): "
    generations = gets.chomp.to_i
    generations = 1000 if generations <= 0
    
    run_evolution_simulation(generations)
  end
  
  def simulate_climate_change_interactive
    puts "\nClimate change scenarios:"
    puts "1. Mild"
    puts "2. Moderate"
    puts "3. Severe"
    puts "4. Catastrophic"
    
    print "Choose scenario (1-4): "
    choice = gets.chomp.to_i
    
    scenarios = [:mild, :moderate, :severe, :catastrophic]
    scenario = scenarios[choice - 1] || :moderate
    
    simulate_climate_change(scenario)
  end
  
  def view_event_log
    puts "\n📜 Event Log"
    puts "============"
    
    if @event_log.empty?
      puts "No events recorded yet."
    else
      @event_log.last(20).each do |event|
        puts "Year #{event[:year]}: #{event[:description]}"
      end
      
      if @event_log.length > 20
        puts "... (showing last 20 of #{@event_log.length} events)"
      end
    end
  end
  
  def create_earth_like_world
    world = create_planetary_system("Solar System")
    
    # Create Earth-like planet
    earth_planet = world[:planets].find { |p| p[:type] == :rocky && p[:habitable] }
    if earth_planet
      create_ecosystem(world, world[:planets].index(earth_planet))
      
      # Add human-like civilization
      if earth_planet[:ecosystem]
        human_species = generate_intelligent_species(earth_planet[:ecosystem])
        human_species[:name] = "Human"
        earth_planet[:ecosystem][:species] << human_species
        
        create_civilization(world, world[:planets].index(earth_planet), earth_planet[:ecosystem][:species].length - 1)
      end
    end
    
    world
  end
  
  def create_alien_world
    world = create_planetary_system("Xenon System")
    
    # Create unique alien planet
    alien_planet = world[:planets].find { |p| p[:type] == :forest && p[:habitable] }
    if alien_planet
      # Modify planet for alien characteristics
      alien_planet[:gravity] = 0.8
      alien_planet[:atmosphere] = true
      alien_planet[:water] = true
      alien_planet[:temperature] = 25
      
      create_ecosystem(world, world[:planets].index(alien_planet))
      
      # Add alien civilization
      if alien_planet[:ecosystem]
        alien_species = generate_intelligent_species(alien_planet[:ecosystem])
        alien_species[:name] = "Xenonian"
        alien_species[:intelligence] = 9
        alien_species[:adaptations] += ["telepathy", "energy_manipulation"]
        alien_planet[:ecosystem][:species] << alien_species
        
        create_civilization(world, world[:planets].index(alien_planet), alien_planet[:ecosystem][:species].length - 1)
      end
    end
    
    world
  end
  
  def create_post_apocalyptic_world
    world = create_planetary_system("Wasteland System")
    
    # Create harsh planet
    wasteland_planet = world[:planets].find { |p| p[:type] == :desert }
    if wasteland_planet
      wasteland_planet[:temperature] = 45
      wasteland_planet[:habitable] = false
      wasteland_planet[:atmosphere] = false
      
      create_ecosystem(world, world[:planets].index(wasteland_planet))
      
      # Add survivor civilization
      if wasteland_planet[:ecosystem]
        survivor_species = generate_intelligent_species(wasteland_planet[:ecosystem])
        survivor_species[:name] = "Survivor"
        survivor_species[:adaptations] += ["radiation_resistance", "water_conservation"]
        wasteland_planet[:ecosystem][:species] << survivor_species
        
        civ = create_civilization(world, world[:planets].index(wasteland_planet), wasteland_planet[:ecosystem][:species].length - 1)
        civ[:government] = :anarchy
        civ[:economy] = :barter
        civ[:population] = 1000
      end
    end
    
    world
  end
  
  def create_utopian_world
    world = create_planetary_system("Paradise System")
    
    # Create perfect planet
    utopia_planet = world[:planets].find { |p| p[:type] == :ocean && p[:habitable] }
    if utopia_planet
      utopia_planet[:temperature] = 22
      utopia_planet[:gravity] = 1.0
      utopia_planet[:atmosphere] = true
      utopia_planet[:water] = true
      
      create_ecosystem(world, world[:planets].index(utopia_planet))
      
      # Add utopian civilization
      if utopia_planet[:ecosystem]
        utopian_species = generate_intelligent_species(utopia_planet[:ecosystem])
        utopian_species[:name] = "Utopian"
        utopian_species[:intelligence] = 10
        utopian_species[:adaptations] += ["peaceful_coexistence", "environmental_harmony"]
        utopia_planet[:ecosystem][:species] << utopian_species
        
        civ = create_civilization(world, world[:planets].index(utopia_planet), utopia_planet[:ecosystem][:species].length - 1)
        civ[:government] = :democracy
        civ[:economy] = :sharing
        civ[:technology_level] = 8
      end
    end
    
    world
  end
  
  def create_dystopian_world
    world = create_planetary_system("Oppression System")
    
    # Create harsh planet
    dystopia_planet = world[:planets].find { |p| p[:type] == :rocky }
    if dystopia_planet
      dystopia_planet[:temperature] = 15
      dystopia_planet[:gravity] = 1.2
      dystopia_planet[:atmosphere] = true
      dystopia_planet[:water] = false
      
      create_ecosystem(world, world[:planets].index(dystopia_planet))
      
      # Add dystopian civilization
      if dystopia_planet[:ecosystem]
        dystopian_species = generate_intelligent_species(dystopia_planet[:ecosystem])
        dystopian_species[:name] = "Oppressed"
        dystopian_species[:intelligence] = 8
        dystopian_species[:adaptations] += ["survival_instinct", "resource_competition"]
        dystopia_planet[:ecosystem][:species] << dystopian_species
        
        civ = create_civilization(world, world[:planets].index(dystopia_planet), dystopia_planet[:ecosystem][:species].length - 1)
        civ[:government] = :dictatorship
        civ[:economy] = :command
        civ[:technology_level] = 6
      end
    end
    
    world
  end
  
  def create_desert_planet
    world = create_planetary_system("Desert System")
    
    # Create desert planet
    desert_planet = world[:planets].find { |p| p[:type] == :desert }
    if desert_planet
      desert_planet[:temperature] = 50
      desert_planet[:gravity] = 0.9
      desert_planet[:atmosphere] = false
      desert_planet[:water] = false
      
      create_ecosystem(world, world[:planets].index(desert_planet))
    end
    
    world
  end
  
  def create_ocean_world
    world = create_planetary_system("Ocean System")
    
    # Create ocean planet
    ocean_planet = world[:planets].find { |p| p[:type] == :ocean }
    if ocean_planet
      ocean_planet[:temperature] = 20
      ocean_planet[:gravity] = 0.8
      ocean_planet[:atmosphere] = true
      ocean_planet[:water] = true
      
      create_ecosystem(world, world[:planets].index(ocean_planet))
    end
    
    world
  end
  
  def create_ice_planet
    world = create_planetary_system("Ice System")
    
    # Create ice planet
    ice_planet = world[:planets].find { |p| p[:type] == :ice_giant }
    if ice_planet
      ice_planet[:temperature] = -50
      ice_planet[:gravity] = 1.5
      ice_planet[:atmosphere] = false
      ice_planet[:water] = false
      
      create_ecosystem(world, world[:planets].index(ice_planet))
    end
    
    world
  end
end

# Main execution
if __FILE__ == $0
  puts "🌍 Virtual World Simulator"
  puts "========================="
  puts "Create and manage entire virtual worlds!"
  puts ""
  
  simulator = VirtualWorldSimulator.new
  
  # Start interactive mode
  simulator.start_world_engine
end
