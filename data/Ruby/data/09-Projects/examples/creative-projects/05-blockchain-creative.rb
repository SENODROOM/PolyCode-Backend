#!/usr/bin/env ruby

# Blockchain-based Creative Project System
# Demonstrates blockchain concepts through a creative application

require 'digest'
require 'json'
require 'date'

# Block class for the creative blockchain
class CreativeBlock
  attr_reader :index, :timestamp, :data, :previous_hash, :hash, :nonce

  def initialize(index, data, previous_hash)
    @index = index
    @timestamp = Time.now
    @data = data
    @previous_hash = previous_hash
    @nonce = 0
    @hash = calculate_hash
  end

  def calculate_hash
    Digest::SHA256.hexdigest(
      "#{@index}#{@timestamp}#{@data.to_json}#{@previous_hash}#{@nonce}"
    )
  end

  def mine_block(difficulty)
    target = "0" * difficulty
    while @hash[0...difficulty] != target
      @nonce += 1
      @hash = calculate_hash
    end
    puts "Block mined: #{@hash}"
  end
end

# Creative blockchain for managing digital art
class CreativeBlockchain
  def initialize
    @chain = [create_genesis_block]
    @difficulty = 2
  end

  def create_genesis_block
    CreativeBlock.new(0, { type: "genesis", artist: "system", title: "Genesis Block" }, "0")
  end

  def get_latest_block
    @chain.last
  end

  def add_block(block)
    block.previous_hash = get_latest_block.hash
    block.mine_block(@difficulty)
    @chain << block
  end

  def chain_valid?
    @chain.each_with_index do |block, index|
      next if index == 0

      previous_block = @chain[index - 1]
      return false if block.previous_hash != previous_block.hash
      return false if block.hash != block.calculate_hash
    end
    true
  end

  def display_chain
    @chain.each do |block|
      puts "Block ##{block.index}"
      puts "Timestamp: #{block.timestamp}"
      puts "Data: #{block.data}"
      puts "Hash: #{block.hash}"
      puts "Previous Hash: #{block.previous_hash}"
      puts "-" * 40
    end
  end
end

# Digital art marketplace
class ArtMarketplace
  def initialize(blockchain)
    @blockchain = blockchain
    @artworks = {}
    @transactions = []
  end

  def register_artwork(artist, title, description, price)
    artwork_id = Digest::SHA256.hexdigest("#{artist}#{title}#{Time.now}")[0...8]
    
    artwork_data = {
      type: "artwork_registration",
      artwork_id: artwork_id,
      artist: artist,
      title: title,
      description: description,
      price: price,
      timestamp: Time.now
    }

    @artworks[artwork_id] = artwork_data
    @blockchain.add_block(CreativeBlock.new(@blockchain.chain.length, artwork_data, ""))
    
    puts "Artwork registered: #{title} by #{artist} (ID: #{artwork_id})"
    artwork_id
  end

  def purchase_artwork(buyer, artwork_id)
    artwork = @artworks[artwork_id]
    return nil unless artwork

    transaction_data = {
      type: "artwork_purchase",
      artwork_id: artwork_id,
      buyer: buyer,
      seller: artwork[:artist],
      price: artwork[:price],
      timestamp: Time.now
    }

    @transactions << transaction_data
    @blockchain.add_block(CreativeBlock.new(@blockchain.chain.length, transaction_data, ""))
    
    puts "Artwork purchased: #{artwork[:title]} by #{buyer} for $#{artwork[:price]}"
    transaction_data
  end

  def verify_ownership(artwork_id, owner)
    # Check blockchain for ownership verification
    @blockchain.chain.each do |block|
      next unless block.data[:artwork_id] == artwork_id
      
      if block.data[:type] == "artwork_purchase" && block.data[:buyer] == owner
        return true
      end
    end
    false
  end

  def get_artwork_history(artwork_id)
    history = []
    @blockchain.chain.each do |block|
      if block.data[:artwork_id] == artwork_id
        history << {
          block_index: block.index,
          timestamp: block.timestamp,
          data: block.data,
          hash: block.hash
        }
      end
    end
    history
  end
end

# Creative NFT generator
class NFTGenerator
  def initialize(blockchain)
    @blockchain = blockchain
    @nfts = {}
  end

  def generate_nft(artist, title, attributes)
    nft_id = Digest::SHA256.hexdigest("#{artist}#{title}#{attributes}#{Time.now}")[0...12]
    
    nft_data = {
      type: "nft_minting",
      nft_id: nft_id,
      artist: artist,
      title: title,
      attributes: attributes,
      token_uri: "https://creative-blockchain.art/nft/#{nft_id}",
      timestamp: Time.now
    }

    @nfts[nft_id] = nft_data
    @blockchain.add_block(CreativeBlock.new(@blockchain.chain.length, nft_data, ""))
    
    puts "NFT minted: #{title} (Token ID: #{nft_id})"
    nft_id
  end

  def transfer_nft(from_address, to_address, nft_id)
    nft = @nfts[nft_id]
    return nil unless nft

    transfer_data = {
      type: "nft_transfer",
      nft_id: nft_id,
      from: from_address,
      to: to_address,
      timestamp: Time.now
    }

    @blockchain.add_block(CreativeBlock.new(@blockchain.chain.length, transfer_data, ""))
    
    puts "NFT transferred: #{nft[:title]} from #{from_address} to #{to_address}"
    transfer_data
  end
end

# Creative collaboration platform
class CreativeCollaboration
  def initialize(blockchain)
    @blockchain = blockchain
    @projects = {}
    @collaborations = []
  end

  def create_project(creator, title, description, max_collaborators)
    project_id = Digest::SHA256.hexdigest("#{creator}#{title}#{Time.now}")[0...8]
    
    project_data = {
      type: "project_creation",
      project_id: project_id,
      creator: creator,
      title: title,
      description: description,
      max_collaborators: max_collaborators,
      collaborators: [creator],
      timestamp: Time.now
    }

    @projects[project_id] = project_data
    @blockchain.add_block(CreativeBlock.new(@blockchain.chain.length, project_data, ""))
    
    puts "Project created: #{title} by #{creator} (ID: #{project_id})"
    project_id
  end

  def join_project(project_id, collaborator, contribution)
    project = @projects[project_id]
    return nil unless project
    return nil if project[:collaborators].length >= project[:max_collaborators]

    collaboration_data = {
      type: "collaboration_join",
      project_id: project_id,
      collaborator: collaborator,
      contribution: contribution,
      timestamp: Time.now
    }

    project[:collaborators] << collaborator
    @collaborations << collaboration_data
    @blockchain.add_block(CreativeBlock.new(@blockchain.chain.length, collaboration_data, ""))
    
    puts "#{collaborator} joined project: #{project[:title]}"
    collaboration_data
  end

  def submit_contribution(project_id, collaborator, contribution)
    contribution_data = {
      type: "contribution_submission",
      project_id: project_id,
      collaborator: collaborator,
      contribution: contribution,
      timestamp: Time.now
    }

    @blockchain.add_block(CreativeBlock.new(@blockchain.chain.length, contribution_data, ""))
    
    puts "Contribution submitted by #{collaborator} to project #{project_id}"
    contribution_data
  end
end

# Demo application
def demo_creative_blockchain
  puts "🎨 Creative Blockchain Demo"
  puts "=" * 50

  # Initialize blockchain
  blockchain = CreativeBlockchain.new
  
  # Initialize marketplace
  marketplace = ArtMarketplace.new(blockchain)
  
  # Initialize NFT generator
  nft_generator = NFTGenerator.new(blockchain)
  
  # Initialize collaboration platform
  collaboration = CreativeCollaboration.new(blockchain)

  puts "\n📦 Registering Artworks..."
  artwork1 = marketplace.register_artwork(
    "Alice Artist", 
    "Digital Sunset", 
    "A beautiful digital painting of a sunset", 
    1000.0
  )
  
  artwork2 = marketplace.register_artwork(
    "Bob Creator", 
    "Abstract Mind", 
    "An abstract representation of consciousness", 
    1500.0
  )

  puts "\n🪙 Minting NFTs..."
  nft1 = nft_generator.generate_nft(
    "Alice Artist",
    "Digital Sunset NFT",
    { style: "digital", colors: 5, rarity: "common" }
  )
  
  nft2 = nft_generator.generate_nft(
    "Bob Creator", 
    "Abstract Mind NFT",
    { style: "abstract", complexity: "high", rarity: "rare" }
  )

  puts "\n💰 Making Purchases..."
  marketplace.purchase_artwork("Charlie Collector", artwork1)
  marketplace.purchase_artwork("Diana Investor", artwork2)

  puts "\n🤝 Creating Collaborative Project..."
  project = collaboration.create_project(
    "Eve Designer",
    "Collective Canvas",
    "A collaborative digital art project",
    5
  )

  collaboration.join_project(project, "Frank Artist", "Background design")
  collaboration.join_project(project, "Grace Illustrator", "Character design")
  
  collaboration.submit_contribution(
    project, 
    "Frank Artist", 
    "Created beautiful gradient background"
  )

  puts "\n🔄 Transferring NFTs..."
  nft_generator.transfer_nft("Alice Artist", "Henry Collector", nft1)

  puts "\n🔍 Verifying Ownership..."
  ownership1 = marketplace.verify_ownership(artwork1, "Charlie Collector")
  ownership2 = marketplace.verify_ownership(artwork2, "Diana Investor")
  
  puts "Charlie owns Digital Sunset: #{ownership1}"
  puts "Diana owns Abstract Mind: #{ownership2}"

  puts "\n📊 Blockchain Validity: #{blockchain.chain_valid?}"

  puts "\n📋 Full Blockchain:"
  blockchain.display_chain

  puts "\n🎯 Demo completed successfully!"
end

# Run the demo
if __FILE__ == $0
  demo_creative_blockchain
end
