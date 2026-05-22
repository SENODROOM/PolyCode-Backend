# Database Examples in Ruby
# Demonstrating database programming patterns and techniques

require 'json'
require 'sqlite3'
require 'pg'
require 'mysql2'
require 'sequel'
require 'active_record'

class DatabaseExamples
  def initialize
    @examples = []
  end
  
  def start_examples
    puts "🗄️ Database Examples in Ruby"
    puts "=============================="
    puts "Explore database programming patterns and techniques!"
    puts ""
    
    interactive_menu
  end
  
  def interactive_menu
    loop do
      puts "\n📋 Database Examples Menu:"
      puts "1. SQLite Database"
      puts "2. PostgreSQL Database"
      puts "3. MySQL Database"
      puts "4. Sequel ORM"
      puts "5. ActiveRecord ORM"
      puts "6. Database Migrations"
      puts "7. Advanced Queries"
      puts "8. Connection Pooling"
      puts "9. Database Transactions"
      puts "10. Database Caching"
      puts "11. View All Examples"
      puts "0. Exit"
      
      print "Choose an example (0-11): "
      choice = gets.chomp.to_i
      
      case choice
      when 1
        sqlite_database
      when 2
        postgresql_database
      when 3
        mysql_database
      when 4
        sequel_orm
      when 5
        activerecord_orm
      when 6
        database_migrations
      when 7
        advanced_queries
      when 8
        connection_pooling
      when 9
        database_transactions
      when 10
        database_caching
      when 11
        show_all_examples
      when 0
        break
      else
        puts "Invalid choice. Please try again."
      end
    end
  end
  
  def sqlite_database
    puts "\n🔹 Example 1: SQLite Database"
    puts "=" * 50
    puts "Working with SQLite database in Ruby."
    puts ""
    
    # SQLite database operations
    puts "🔧 SQLite Database Operations:"
    
    require 'sqlite3'
    
    # Create database connection
    db = SQLite3::Database.new('example.db')
    
    # Create table
    puts "Creating users table..."
    db.execute <<-SQL
      CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        age INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
      )
    SQL
    
    # Insert data
    puts "Inserting sample data..."
    db.execute("INSERT INTO users (name, email, age) VALUES (?, ?, ?)", 
                ["Alice", "alice@test.com", 30])
    db.execute("INSERT INTO users (name, email, age) VALUES (?, ?, ?)", 
                ["Bob", "bob@test.com", 25])
    db.execute("INSERT INTO users (name, email, age) VALUES (?, ?, ?)", 
                ["Charlie", "charlie@test.com", 35])
    
    # Query data
    puts "Querying data..."
    users = db.execute("SELECT * FROM users ORDER BY age DESC")
    
    puts "Users in database:"
    users.each do |user|
      puts "  ID: #{user[0]}, Name: #{user[1]}, Email: #{user[2]}, Age: #{user[3]}"
    end
    
    # Update data
    puts "\nUpdating user data..."
    db.execute("UPDATE users SET age = ? WHERE name = ?", [31, "Alice"])
    
    # Delete data
    puts "Deleting user..."
    db.execute("DELETE FROM users WHERE name = ?", ["Charlie"])
    
    # Query after operations
    puts "\nFinal database state:"
    final_users = db.execute("SELECT * FROM users")
    final_users.each do |user|
      puts "  ID: #{user[0]}, Name: #{user[1]}, Email: #{user[2]}, Age: #{user[3]}"
    end
    
    # Prepared statements
    puts "\nUsing prepared statements..."
    stmt = db.prepare("INSERT INTO users (name, email, age) VALUES (?, ?, ?)")
    stmt.execute("David", "david@test.com", 28)
    stmt.execute("Eve", "eve@test.com", 27)
    stmt.close
    
    # Transaction example
    puts "\nTransaction example..."
    db.transaction do
      db.execute("INSERT INTO users (name, email, age) VALUES (?, ?, ?)", 
                  ["Frank", "frank@test.com", 40])
      db.execute("INSERT INTO users (name, email, age) VALUES (?, ?, ?)", 
                  ["Grace", "grace@test.com", 32])
    end
    
    # Close database
    db.close
    
    # Clean up
    File.delete('example.db') if File.exist?('example.db')
    
    @examples << {
      title: "SQLite Database",
      description: "Working with SQLite database using sqlite3 gem",
      code: <<~RUBY
        require 'sqlite3'
        db = SQLite3::Database.new('example.db')
        db.execute("CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT)")
        db.execute("INSERT INTO users (name) VALUES (?)", ["Alice"])
        users = db.execute("SELECT * FROM users")
      RUBY
    }
    
    puts "\n✅ SQLite Database example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def postgresql_database
    puts "\n🐘 Example 2: PostgreSQL Database"
    puts "=" * 50
    puts "Working with PostgreSQL database in Ruby."
    puts ""
    
    # PostgreSQL database operations
    puts "🔧 PostgreSQL Database Operations:"
    
    require 'pg'
    
    begin
      # Connect to PostgreSQL
      puts "Connecting to PostgreSQL..."
      conn = PG.connect(
        host: 'localhost',
        dbname: 'test_db',
        user: 'postgres',
        password: 'password'
      )
      
      # Create table
      puts "Creating products table..."
      conn.exec <<-SQL
        CREATE TABLE IF NOT EXISTS products (
          id SERIAL PRIMARY KEY,
          name VARCHAR(255) NOT NULL,
          price DECIMAL(10, 2) NOT NULL,
          category VARCHAR(100),
          in_stock BOOLEAN DEFAULT TRUE,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
      SQL
      
      # Insert data with RETURNING clause
      puts "Inserting products..."
      result = conn.exec_params(
        "INSERT INTO products (name, price, category) VALUES ($1, $2, $3) RETURNING id",
        ["Laptop", 999.99, "Electronics"]
      )
      product_id = result[0]['id']
      puts "Inserted product with ID: #{product_id}"
      
      # Batch insert
      puts "Batch inserting products..."
      conn.exec_params(
        "INSERT INTO products (name, price, category) VALUES ($1, $2, $3), ($4, $5, $6)",
        ["Mouse", 29.99, "Electronics", "Keyboard", 49.99, "Electronics"]
      )
      
      # Query with parameters
      puts "Querying products..."
      result = conn.exec_params(
        "SELECT * FROM products WHERE category = $1 AND price > $2",
        ["Electronics", 50]
      )
      
      puts "Expensive electronics:"
      result.each do |row|
        puts "  ID: #{row['id']}, Name: #{row['name']}, Price: $#{row['price']}"
      end
      
      # Update with RETURNING
      puts "Updating product..."
      update_result = conn.exec_params(
        "UPDATE products SET price = $1 WHERE id = $2 RETURNING price",
        [899.99, product_id]
      )
      puts "Updated price to: $#{update_result[0]['price']}"
      
      # JSON operations
      puts "JSON operations..."
      conn.exec("INSERT INTO products (name, price, category) VALUES ($1, $2, $3)", 
                ["Smartphone", 699.99, "Electronics"])
      
      json_result = conn.exec("SELECT json_build_object('name', name, 'price', price) AS product FROM products WHERE name = 'Smartphone'")
      json_result.each do |row|
        puts "JSON product: #{row['product']}"
      end
      
      # Array operations
      puts "Array operations..."
      array_result = conn.exec("SELECT ARRAY[1, 2, 3, 4, 5] AS numbers")
      puts "Array result: #{array_result[0]['numbers']}"
      
      # Close connection
      conn.close
      
    rescue PG::Error => e
      puts "PostgreSQL error: #{e.message}"
      puts "Note: Make sure PostgreSQL is running and accessible"
    end
    
    @examples << {
      title: "PostgreSQL Database",
      description: "Working with PostgreSQL database using pg gem",
      code: <<~RUBY
        require 'pg'
        conn = PG.connect(host: 'localhost', dbname: 'test_db')
        conn.exec("CREATE TABLE products (id SERIAL PRIMARY KEY, name VARCHAR(255))")
        result = conn.exec_params("SELECT * FROM products WHERE category = $1", ["Electronics"])
        conn.close
      RUBY
    }
    
    puts "\n✅ PostgreSQL Database example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def mysql_database
    puts "\n🐬 Example 3: MySQL Database"
    puts "=" * 50
    puts "Working with MySQL database in Ruby."
    puts ""
    
    # MySQL database operations
    puts "🔧 MySQL Database Operations:"
    
    require 'mysql2'
    
    begin
      # Connect to MySQL
      puts "Connecting to MySQL..."
      client = Mysql2::Client.new(
        host: 'localhost',
        username: 'root',
        password: 'password',
        database: 'test_db'
      )
      
      # Create table
      puts "Creating orders table..."
      client.query <<-SQL
        CREATE TABLE IF NOT EXISTS orders (
          id INT AUTO_INCREMENT PRIMARY KEY,
          customer_name VARCHAR(255) NOT NULL,
          order_date DATETIME NOT NULL,
          total_amount DECIMAL(10, 2) NOT NULL,
          status ENUM('pending', 'processing', 'shipped', 'delivered') DEFAULT 'pending',
          items JSON,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
      SQL
      
      # Insert data
      puts "Inserting orders..."
      insert_stmt = client.prepare("INSERT INTO orders (customer_name, order_date, total_amount, status, items) VALUES (?, ?, ?, ?, ?, ?)")
      
      order1_items = [
        { product: "Laptop", quantity: 1, price: 999.99 },
        { product: "Mouse", quantity: 1, price: 29.99 }
      ]
      
      insert_stmt.execute("Alice Johnson", Time.now, 1029.98, "pending", order1_items.to_json)
      
      order2_items = [
        { product: "Keyboard", quantity: 2, price: 49.99 },
        { product: "Monitor", quantity: 1, price: 299.99 }
      ]
      
      insert_stmt.execute("Bob Smith", Time.now, 399.97, "processing", order2_items.to_json)
      
      # Query data
      puts "Querying orders..."
      orders = client.query("SELECT * FROM orders ORDER BY order_date DESC")
      
      puts "Orders in database:"
      orders.each do |order|
        items = JSON.parse(order['items'])
        puts "  Order ##{order['id']}: #{order['customer_name']} - $#{order['total_amount']} (#{order['status']})"
        puts "    Items: #{items.map { |item| "#{item['quantity']}x #{item['product']}" }.join(', ')}"
      end
      
      # Update data
      puts "\nUpdating order status..."
      update_stmt = client.prepare("UPDATE orders SET status = ? WHERE id = ?")
      update_stmt.execute("shipped", 1)
      
      # JSON query
      puts "JSON query operations..."
      json_orders = client.query("SELECT id, customer_name, JSON_EXTRACT(items, '$[0].product') AS first_item FROM orders")
      
      puts "Orders with first item extracted:"
      json_orders.each do |order|
        puts "  Order ##{order['id']}: #{order['customer_name']} - First item: #{order['first_item']}"
      end
      
      # Aggregate functions
      puts "\nAggregate functions:"
      stats = client.query("SELECT COUNT(*) as total_orders, SUM(total_amount) as total_revenue, AVG(total_amount) as avg_order FROM orders")
      stats.each do |stat|
        puts "  Total orders: #{stat['total_orders']}"
        puts "  Total revenue: $#{stat['total_revenue']}"
        puts "  Average order: $#{stat['avg_order']}"
      end
      
      # Close connection
      client.close
      
    rescue Mysql2::Error => e
      puts "MySQL error: #{e.message}"
      puts "Note: Make sure MySQL is running and accessible"
    end
    
    @examples << {
      title: "MySQL Database",
      description: "Working with MySQL database using mysql2 gem",
      code: <<~RUBY
        require 'mysql2'
        client = Mysql2::Client.new(host: 'localhost', username: 'root')
        client.query("CREATE TABLE orders (id INT AUTO_INCREMENT PRIMARY KEY)")
        stmt = client.prepare("INSERT INTO orders (name) VALUES (?)")
        stmt.execute("Alice")
        orders = client.query("SELECT * FROM orders")
        client.close
      RUBY
    }
    
    puts "\n✅ MySQL Database example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def sequel_orm
    puts "\n🗄️ Example 4: Sequel ORM"
    puts "=" * 50
    puts "Working with databases using Sequel ORM."
    puts ""
    
    # Sequel ORM operations
    puts "🔧 Sequel ORM Operations:"
    
    require 'sequel'
    
    # Create in-memory database for demo
    DB = Sequel.sqlite(':memory:')
    
    # Define model
    puts "Defining User model..."
    class User < Sequel::Model
      set_dataset :users
      
      # Define columns
      plugin :validation_helpers
      plugin :json_serializer
      
      def validate
        super
        errors.add(:email, "is invalid") unless email =~ /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i
        errors.add(:age, "must be between 0 and 150") unless age && (0..150).include?(age)
      end
    end
    
    # Create table
    puts "Creating users table..."
    DB.create_table(:users) do
      primary_key :id
      String :name, null: false
      String :email, null: false, unique: true
      Integer :age
      DateTime :created_at, default: Sequel::CURRENT_TIMESTAMP
      DateTime :updated_at, default: Sequel::CURRENT_TIMESTAMP
    end
    
    # Create users
    puts "Creating users..."
    User.create(name: "Alice", email: "alice@test.com", age: 30)
    User.create(name: "Bob", email: "bob@test.com", age: 25)
    User.create(name: "Charlie", email: "charlie@test.com", age: 35)
    
    # Query users
    puts "Querying users..."
    all_users = User.all
    puts "All users:"
    all_users.each do |user|
      puts "  #{user.name} (#{user.email}) - Age: #{user.age}"
    end
    
    # Advanced queries
    puts "\nAdvanced queries:"
    
    # Where clause
    adults = User.where(Sequel.lit(age: 30..100))
    puts "Adults (age >= 30): #{adults.map(&:name).join(', ')}"
    
    # Order and limit
    recent_users = User.order(Sequel.desc(:created_at)).limit(2)
    puts "Most recent users: #{recent_users.map(&:name).join(', ')}"
    
    # Join operations
    puts "\nJoin operations..."
    
    # Create posts table
    DB.create_table(:posts) do
      primary_key :id
      foreign_key :user_id, :users
      String :title, null: false
      Text :content
      DateTime :created_at, default: Sequel::CURRENT_TIMESTAMP
    end
    
    class Post < Sequel::Model
      set_dataset :posts
      many_to_one :user, key: :user_id
    end
    
    # Create posts
    Post.create(user_id: 1, title: "Alice's First Post", content: "Hello from Alice!")
    Post.create(user_id: 2, title: "Bob's Post", content: "Bob is here!")
    Post.create(user_id: 1, title: "Alice's Second Post", content: "Another post from Alice!")
    
    # Join query
    users_with_posts = User.left_join(:posts, user_id: :id).select(
      :users__id,
      :users__name,
      :users__email,
      :posts__title,
      :posts__content
    )
    
    puts "Users with their posts:"
    users_with_posts.each do |row|
      if row[:posts__title]
        puts "  #{row[:users__name]}: #{row[:posts__title]}"
      else
        puts "  #{row[:users__name]}: No posts"
      end
    end
    
    # Aggregation
    puts "\nAggregation queries:"
    user_stats = User.select(
      :name,
      Sequel.as(Sequel.lit(:posts__id).count, :post_count)
    ).left_join(:posts, user_id: :id).group(:name)
    
    puts "User post counts:"
    user_stats.each do |stat|
      puts "  #{stat[:name]}: #{stat[:post_count]} posts"
    end
    
    # Transactions
    puts "\nTransaction example:"
    DB.transaction do
      User.create(name: "David", email: "david@test.com", age: 28)
      User.create(name: "Eve", email: "eve@test.com", age: 27)
      puts "Transaction completed - users created"
    end
    
    # Validation
    puts "\nValidation example:"
    begin
      User.create(name: "Invalid User", email: "invalid-email", age: -5)
    rescue Sequel::ValidationFailed => e
      puts "Validation errors:"
      e.errors.each do |error|
        puts "  #{error[0]}: #{error[1]}"
      end
    end
    
    @examples << {
      title: "Sequel ORM",
      description: "Working with databases using Sequel ORM",
      code: <<~RUBY
        require 'sequel'
        DB = Sequel.sqlite('database.db')
        
        class User < Sequel::Model
          set_dataset :users
        end
        
        User.create(name: "Alice", email: "alice@test.com")
        users = User.where(age: 30..100)
        user = User[1]  # Find by primary key
      RUBY
    }
    
    puts "\n✅ Sequel ORM example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def activerecord_orm
    puts "\n🗄️ Example 5: ActiveRecord ORM"
    puts "=" * 50
    puts "Working with databases using ActiveRecord ORM."
    puts ""
    
    # ActiveRecord ORM operations
    puts "🔧 ActiveRecord ORM Operations:"
    
    require 'active_record'
    
    # Configure database (in-memory SQLite for demo)
    ActiveRecord::Base.establish_connection(
      adapter: 'sqlite3',
      database: ':memory:'
    )
    
    # Define model
    puts "Defining models..."
    
    class ApplicationRecord < ActiveRecord::Base
      self.abstract_class = true
    end
    
    class User < ApplicationRecord
      validates :name, presence: true
      validates :email, presence: true, uniqueness: true, format: { with: /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i }
      validates :age, numericality: { greater_than_or_equal_to: 0, less_than_or_equal_to: 150 }
      
      has_many :posts, dependent: :destroy
      
      def full_name
        "#{name} (#{email})"
      end
      
      def adult?
        age >= 18
      end
      
      scope :adults, -> { where(age: 18..150) }
      scope :recent, -> { order(created_at: :desc) }
      scope :by_email, ->(email) { where(email: email) }
    end
    
    class Post < ApplicationRecord
      belongs_to :user
      validates :title, presence: true
      validates :content, presence: true, length: { minimum: 10 }
      
      scope :recent, -> { order(created_at: :desc) }
      scope :by_user, ->(user) { where(user: user) }
    end
    
    # Create tables
    puts "Creating database tables..."
    
    ActiveRecord::Schema.define do
      create_table :users do |t|
        t.string :name, null: false
        t.string :email, null: false
        t.integer :age
        t.timestamps
      end
      
      create_table :posts do |t|
        t.references :user, null: false, foreign_key: true
        t.string :title, null: false
        t.text :content, null: false
        t.timestamps
      end
    end
    
    # Create records
    puts "Creating records..."
    
    alice = User.create!(name: "Alice", email: "alice@test.com", age: 30)
    bob = User.create!(name: "Bob", email: "bob@test.com", age: 25)
    charlie = User.create!(name: "Charlie", email: "charlie@test.com", age: 35)
    
    # Create posts
    alice.posts.create!(title: "Alice's First Post", content: "This is Alice's first post content. It's quite long enough to pass validation.")
    alice.posts.create!(title: "Alice's Second Post", content: "Alice's second post with sufficient content to meet minimum length requirements.")
    bob.posts.create!(title: "Bob's Post", content: "Bob is posting something interesting here with enough content to be valid.")
    
    # Query operations
    puts "\nQuery operations:"
    
    # Find operations
    puts "Find operations:"
    puts "  User by ID: #{User.find(1).name}"
    puts "  User by email: #{User.find_by(email: 'bob@test.com').name}"
    puts "  First user: #{User.first.name}"
    puts "  Last user: #{User.last.name}"
    
    # Where queries
    puts "\nWhere queries:"
    adults = User.where(age: 18..150)
    puts "  Adults: #{adults.map(&:name).join(', ')}"
    
    recent_users = User.recent.limit(2)
    puts "  Recent users: #{recent_users.map(&:name).join(', ')}"
    
    # Scopes
    puts "\nScope usage:"
    adult_users = User.adults.recent
    puts "  Recent adults: #{adult_users.map(&:name).join(', ')}"
    
    # Associations
    puts "\nAssociation operations:"
    puts "  Alice's posts: #{alice.posts.count}"
    puts "  Bob's posts: #{bob.posts.count}"
    
    alice_posts = alice.posts.recent
    puts "  Alice's recent posts:"
    alice_posts.each do |post|
      puts "    #{post.title}"
    end
    
    # Advanced queries
    puts "\nAdvanced queries:"
    
    # Joins
    users_with_post_count = User.left_joins(:posts)
      .select('users.*, COUNT(posts.id) as post_count')
      .group('users.id')
    
    puts "Users with post counts:"
    users_with_post_count.each do |user|
      puts "  #{user.name}: #{user.post_count} posts"
    end
    
    # Subqueries
    users_with_recent_posts = User.where(
      id: Post.select(:user_id).where('created_at > ?', 1.week.ago)
    )
    
    puts "Users with recent posts: #{users_with_recent_posts.map(&:name).join(', ')}"
    
    # Calculations
    puts "\nCalculations:"
    puts "  Total users: #{User.count}"
    puts "  Average age: #{User.average(:age).round(2)}"
    puts "  Age range: #{User.minimum(:age)} - #{User.maximum(:age)}"
    
    # Validation
    puts "\nValidation examples:"
    
    begin
      User.create!(name: "", email: "invalid", age: -5)
    rescue ActiveRecord::RecordInvalid => e
      puts "  Validation errors:"
      e.errors.full_messages.each do |error|
        puts "    #{error}"
      end
    end
    
    # Callbacks
    puts "\nCallback example:"
    
    class UserWithCallbacks < ApplicationRecord
      before_create :set_defaults
      after_create :send_welcome
      before_update :log_update
      after_destroy :cleanup_user_data
      
      private
      
      def set_defaults
        self.age ||= 18
      end
      
      def send_welcome
        puts "  [Callback] Welcome email sent to #{name}"
      end
      
      def log_update
        puts "  [Callback] User #{name} updated"
      end
      
      def cleanup_user_data
        puts "  [Callback] Cleaning up data for user #{name}"
      end
    end
    
    user_with_callbacks = UserWithCallbacks.create!(name: "David", email: "david@test.com", age: 28)
    user_with_callbacks.update!(age: 29)
    user_with_callbacks.destroy!
    
    @examples << {
      title: "ActiveRecord ORM",
      description: "Working with databases using ActiveRecord ORM",
      code: <<~RUBY
        class User < ApplicationRecord
          validates :name, presence: true
          validates :email, presence: true, uniqueness: true
          has_many :posts, dependent: :destroy
          
          scope :adults, -> { where(age: 18..150) }
          scope :recent, -> { order(created_at: :desc) }
        end
        
        user = User.create!(name: "Alice", email: "alice@test.com")
        adults = User.adults
        user.posts.create!(title: "My Post", content: "Content here")
      RUBY
    }
    
    puts "\n✅ ActiveRecord ORM example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def database_migrations
    puts "\n🔄 Example 6: Database Migrations"
    puts "=" * 50
    puts "Managing database schema changes with migrations."
    puts ""
    
    # Migration examples
    puts "🔧 Database Migration Patterns:"
    
    # Sequel migration
    puts "Sequel migration:"
    
    Sequel.migration do
      change do
        create_table(:users) do
          primary_key :id
          String :name, null: false
          String :email, null: false, unique: true
          Integer :age
          DateTime :created_at, default: Sequel::CURRENT_TIMESTAMP
        end
        
        add_column :users, :status, String, default: 'active'
        add_index :users, :email
      end
    end
    
    # ActiveRecord migration
    puts "\nActiveRecord migration:"
    
    class CreateUsersTable < ActiveRecord::Migration[7.0]
      def change
        create_table :users do |t|
          t.string :name, null: false
          t.string :email, null: false
          t.integer :age
          t.string :status, default: 'active'
          t.timestamps
        end
        
        add_index :users, :email, unique: true
      end
    end
    
    class AddStatusToUsers < ActiveRecord::Migration[7.0]
      def change
        add_column :users, :status, :string, default: 'active'
        add_index :users, :status
      end
    end
    
    # Migration with data transformation
    puts "\nMigration with data transformation:"
    
    class NormalizeEmails < ActiveRecord::Migration[7.0]
      def up
        # Add new column
        add_column :users, :email_normalized, :string
        
        # Normalize existing emails
        User.find_each(batch_size: 1000) do |user|
          user.update!(
            email_normalized: user.email.downcase.strip
          )
        end
        
        # Remove old column
        remove_column :users, :email
        rename_column :users, :email_normalized, :email
      end
      
      def down
        add_column :users, :email_old, :string
        User.find_each(batch_size: 1000) do |user|
          user.update!(
            email_old: user.email
          )
        end
        remove_column :users, :email
        rename_column :users, :email_old, :email
      end
    end
    
    # Complex migration example
    puts "\nComplex migration example:"
    
    class CreatePostsAndAddUserRelation < ActiveRecord::Migration[7.0]
      def change
        create_table :posts do |t|
          t.string :title, null: false
          t.text :content, null: false
          t.references :user, null: false, foreign_key: true
          t.timestamps
        end
        
        # Add foreign key constraint
        add_foreign_key :posts, :users, column: :user_id
        
        # Create join table for many-to-many relationship
        create_table :user_roles do |t|
          t.references :user, null: false, foreign_key: true
          t.references :role, null: false, foreign_key: true
          t.timestamps
        end
        
        add_index :user_roles, [:user_id, :role_id], unique: true
      end
    end
    
    # Migration with raw SQL
    puts "\nMigration with raw SQL:"
    
    class AddFullTextSearch < ActiveRecord::Migration[7.0]
      def up
        execute <<-SQL
          CREATE INDEX posts_search_idx ON posts USING gin(to_tsvector('english', title || ' ' || content));
          CREATE OR REPLACE FUNCTION search_posts(query text) RETURNS table(id integer, rank real) AS $$
            BEGIN
              RETURN QUERY
              SELECT id, ts_rank(search_vector, query) as rank
              FROM posts, plainto_tsquery('english', query) AS query
              WHERE search_vector @@ query
              ORDER BY rank DESC
              LIMIT 10;
            END;
          $$ LANGUAGE plpgsql;
        SQL
      end
      
      def down
        execute "DROP INDEX IF EXISTS posts_search_idx;"
        execute "DROP FUNCTION IF EXISTS search_posts(text);"
      end
    end
    
    @examples << {
      title: "Database Migrations",
      description: "Managing database schema changes with migrations",
      code: <<~RUBY
        class CreateUsersTable < ActiveRecord::Migration[7.0]
          def change
            create_table :users do |t|
              t.string :name, null: false
              t.string :email, null: false
              t.timestamps
            end
          end
        end
        
        Sequel.migration do
          change do
            create_table(:users) do
              primary_key :id
              String :name, null: false
            end
          end
        end
      RUBY
    }
    
    puts "\n✅ Database Migrations example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def show_all_examples
    puts "\n📚 All Database Examples"
    puts "=" * 50
    
    @examples.each_with_index do |example, index|
      puts "\n#{index + 1}. #{example[:title]}"
      puts "   Description: #{example[:description]}"
      puts "   Key features: #{example[:code].split("\n").first(3)}..."
    end
    
    puts "\nTotal examples: #{@examples.length}"
    puts "All examples demonstrate different aspects of database programming in Ruby!"
  end
end

# Main execution
if __FILE__ == $0
  examples = DatabaseExamples.new
  examples.start_examples
end
