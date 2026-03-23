# Audio System for Game Engine
# Handles sound effects, music, and audio management

class AudioSystem
  def initialize
    @sounds = {}
    @music = {}
    @active_sounds = []
    @current_music = nil
    @music_volume = 1.0
    @sound_volume = 1.0
    @muted = false
  end

  # Load a sound effect
  def load_sound(name, file_path)
    @sounds[name] = {
      file: file_path,
      loaded: true,
      volume: 1.0
    }
    puts "Loaded sound: #{name} from #{file_path}"
  end

  # Load background music
  def load_music(name, file_path)
    @music[name] = {
      file: file_path,
      loaded: true,
      volume: 1.0,
      looping: true
    }
    puts "Loaded music: #{name} from #{file_path}"
  end

  # Play a sound effect
  def play_sound(name, volume = nil, pitch = 1.0)
    return if @muted
    
    sound = @sounds[name]
    return unless sound && sound[:loaded]

    sound_volume = volume || sound[:volume]
    final_volume = sound_volume * @sound_volume
    
    # Simulate playing sound
    sound_instance = {
      name: name,
      volume: final_volume,
      pitch: pitch,
      start_time: Time.now,
      duration: estimate_sound_duration(name)
    }
    
    @active_sounds << sound_instance
    puts "Playing sound: #{name} at volume #{final_volume}"
    
    # Remove sound after it finishes
    Thread.new do
      sleep(sound_instance[:duration])
      @active_sounds.delete(sound_instance)
    end
  end

  # Play background music
  def play_music(name, looping = true, volume = nil)
    return if @muted
    
    music = @music[name]
    return unless music && music[:loaded]

    # Stop current music if playing
    stop_music if @current_music

    music_volume = volume || music[:volume]
    final_volume = music_volume * @music_volume
    
    @current_music = {
      name: name,
      volume: final_volume,
      looping: looping,
      start_time: Time.now
    }
    
    puts "Playing music: #{name} at volume #{final_volume}"
  end

  # Stop current music
  def stop_music
    if @current_music
      puts "Stopped music: #{@current_music[:name]}"
      @current_music = nil
    end
  end

  # Stop all sounds
  def stop_all_sounds
    @active_sounds.clear
    puts "Stopped all sounds"
  end

  # Set master music volume
  def set_music_volume(volume)
    @music_volume = [0.0, [1.0, volume].min].max
    puts "Music volume set to #{@music_volume}"
  end

  # Set master sound volume
  def set_sound_volume(volume)
    @sound_volume = [0.0, [1.0, volume].min].max
    puts "Sound volume set to #{@sound_volume}"
  end

  # Mute/unmute all audio
  def toggle_mute
    @muted = !@muted
    if @muted
      stop_music
      stop_all_sounds
      puts "Audio muted"
    else
      puts "Audio unmuted"
    end
  end

  # Check if audio is muted
  def muted?
    @muted
  end

  # Get currently playing music
  def current_music
    @current_music ? @current_music[:name] : nil
  end

  # Get list of active sounds
  def active_sounds
    @active_sounds.map { |sound| sound[:name] }
  end

  # Update audio system (call each frame)
  def update(delta_time)
    # Clean up finished sounds
    current_time = Time.now
    @active_sounds.reject! do |sound|
      (current_time - sound[:start_time]) >= sound[:duration]
    end
  end

  # Create 3D audio effect
  def play_3d_sound(name, x, y, z, listener_x, listener_y, listener_z)
    return if @muted
    
    # Calculate distance and volume based on 3D position
    distance = Math.sqrt((x - listener_x)**2 + (y - listener_y)**2 + (z - listener_z)**2)
    max_distance = 100.0  # Maximum hearing distance
    
    return if distance > max_distance
    
    # Calculate volume based on distance (inverse square law)
    volume = [1.0 - (distance / max_distance), 0.0].max
    
    # Calculate pan based on x position
    pan = (x - listener_x) / max_distance
    pan = [-1.0, [1.0, pan].min].max
    
    puts "Playing 3D sound: #{name} at distance #{distance.round(2)}, volume #{volume.round(2)}, pan #{pan.round(2)}"
    
    play_sound(name, volume)
  end

  # Create audio fade effect
  def fade_music(target_volume, duration)
    return unless @current_music
    
    start_volume = @current_music[:volume]
    volume_change = target_volume - start_volume
    steps = (duration * 10).to_i  # 10 updates per second
    volume_step = volume_change / steps
    
    Thread.new do
      steps.times do
        @current_music[:volume] += volume_step
        sleep(duration / steps)
      end
      @current_music[:volume] = target_volume
    end
  end

  # Load audio from configuration
  def load_audio_config(config)
    config[:sounds]&.each do |name, sound_config|
      load_sound(name, sound_config[:file])
      @sounds[name][:volume] = sound_config[:volume] if sound_config[:volume]
    end
    
    config[:music]&.each do |name, music_config|
      load_music(name, music_config[:file])
      @music[name][:volume] = music_config[:volume] if music_config[:volume]
      @music[name][:looping] = music_config[:looping] if music_config[:looping]
    end
  end

  # Get audio statistics
  def get_stats
    {
      loaded_sounds: @sounds.keys.length,
      loaded_music: @music.keys.length,
      active_sounds: @active_sounds.length,
      current_music: current_music,
      music_volume: @music_volume,
      sound_volume: @sound_volume,
      muted: @muted?
    }
  end

  private

  # Estimate sound duration (in a real engine, this would be calculated from the audio file)
  def estimate_sound_duration(name)
    # Simple duration estimation based on sound name
    case name.to_s
    when /explosion/i then 2.0
    when /shoot/i then 0.5
    when /footstep/i then 0.3
    when /jump/i then 0.8
    when /pickup/i then 0.4
    else 1.0
    end
  end
end

# Audio manager for coordinating multiple audio systems
class AudioManager
  def initialize
    @systems = {}
    @global_volume = 1.0
  end

  # Create and register an audio system
  def create_system(name)
    system = AudioSystem.new
    @systems[name] = system
    system
  end

  # Get an audio system by name
  def get_system(name)
    @systems[name]
  end

  # Set global volume for all systems
  def set_global_volume(volume)
    @global_volume = [0.0, [1.0, volume].min].max
    @systems.each_value do |system|
      system.set_music_volume(@global_volume)
      system.set_sound_volume(@global_volume)
    end
  end

  # Update all audio systems
  def update_all(delta_time)
    @systems.each_value { |system| system.update(delta_time) }
  end

  # Mute all systems
  def mute_all
    @systems.each_value { |system| system.toggle_mute if !system.muted? }
  end

  # Unmute all systems
  def unmute_all
    @systems.each_value { |system| system.toggle_mute if system.muted? }
  end
end

# Example usage
if __FILE__ == $0
  puts "🎵 Audio System Demo"
  puts "=" * 30

  # Create audio system
  audio = AudioSystem.new

  # Load some sounds
  audio.load_sound(:explosion, "sounds/explosion.wav")
  audio.load_sound(:shoot, "sounds/shoot.wav")
  audio.load_sound(:pickup, "sounds/pickup.wav")

  # Load music
  audio.load_music(:background, "music/background.mp3")
  audio.load_music(:menu, "music/menu.mp3")

  # Play background music
  audio.play_music(:background)

  # Play some sounds
  audio.play_sound(:shoot)
  sleep(0.5)
  audio.play_sound(:explosion)
  sleep(1.0)
  audio.play_sound(:pickup)

  # Test volume controls
  audio.set_music_volume(0.5)
  audio.set_sound_volume(0.8)

  # Test 3D audio
  audio.play_3d_sound(:explosion, 50, 0, 0, 0, 0, 0)
  audio.play_3d_sound(:pickup, 10, 10, 0, 0, 0, 0)

  # Test fade effect
  puts "Fading out music..."
  audio.fade_music(0.0, 2.0)
  sleep(2.5)

  # Get statistics
  stats = audio.get_stats
  puts "\nAudio Statistics:"
  stats.each { |key, value| puts "  #{key}: #{value}" }

  puts "\n🎵 Demo completed!"
end
