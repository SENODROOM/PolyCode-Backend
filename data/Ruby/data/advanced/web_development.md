# Web Development with Ruby

## Overview

Ruby offers powerful frameworks and tools for web development, from the popular Ruby on Rails framework to lightweight Sinatra applications and modern API development.

## Ruby on Rails

### Getting Started with Rails

```bash
# Install Rails
gem install rails

# Create new Rails app
rails new my_app
cd my_app

# Start the server
rails server
```

### MVC Architecture

```ruby
# app/models/user.rb
class User < ApplicationRecord
  validates :name, presence: true
  validates :email, presence: true, uniqueness: true
  
  has_many :posts
  has_many :comments
  
  def full_name
    "#{first_name} #{last_name}"
  end
end

# app/controllers/users_controller.rb
class UsersController < ApplicationController
  before_action :set_user, only: [:show, :edit, :update, :destroy]
  
  def index
    @users = User.all
  end
  
  def show
  end
  
  def new
    @user = User.new
  end
  
  def create
    @user = User.new(user_params)
    
    if @user.save
      redirect_to @user, notice: 'User was successfully created.'
    else
      render :new
    end
  end
  
  def update
    if @user.update(user_params)
      redirect_to @user, notice: 'User was successfully updated.'
    else
      render :edit
    end
  end
  
  def destroy
    @user.destroy
    redirect_to users_url, notice: 'User was successfully deleted.'
  end
  
  private
  
  def set_user
    @user = User.find(params[:id])
  end
  
  def user_params
    params.require(:user).permit(:name, :email, :first_name, :last_name)
  end
end

# app/views/users/index.html.erb
<h1>Users</h1>

<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th colspan="3"></th>
    </tr>
  </thead>
  <tbody>
    <% @users.each do |user| %>
      <tr>
        <td><%= user.full_name %></td>
        <td><%= user.email %></td>
        <td><%= link_to 'Show', user %></td>
        <td><%= link_to 'Edit', edit_user_path(user) %></td>
        <td><%= link_to 'Destroy', user, method: :delete, data: { confirm: 'Are you sure?' } %></td>
      </tr>
    <% end %>
  </tbody>
</table>

<%= link_to 'New User', new_user_path %>
```

### Database Migrations

```ruby
# db/migrate/001_create_users.rb
class CreateUsers < ActiveRecord::Migration[7.0]
  def change
    create_table :users do |t|
      t.string :first_name, null: false
      t.string :last_name, null: false
      t.string :email, null: false
      t.timestamps
    end
    
    add_index :users, :email, unique: true
  end
end

# db/migrate/002_create_posts.rb
class CreatePosts < ActiveRecord::Migration[7.0]
  def change
    create_table :posts do |t|
      t.references :user, null: false, foreign_key: true
      t.string :title, null: false
      t.text :content
      t.string :status, default: 'draft'
      t.timestamps
    end
    
    add_index :posts, :user_id
    add_index :posts, :status
  end
end
```

### Active Record Associations

```ruby
class Post < ApplicationRecord
  belongs_to :user
  has_many :comments, dependent: :destroy
  has_many :tags, through: :post_tags
  
  validates :title, presence: true, length: { minimum: 5 }
  validates :content, presence: true
  validates :status, inclusion: { in: %w[draft published archived] }
  
  scope :published, -> { where(status: 'published') }
  scope :recent, -> { order(created_at: :desc) }
  
  def published?
    status == 'published'
  end
  
  def word_count
    content.split(/\s+/).length
  end
  
  def summary(length: 100)
    content.length > length ? "#{content[0...length]}..." : content
  end
end

class Comment < ApplicationRecord
  belongs_to :post
  belongs_to :user
  
  validates :content, presence: true, length: { minimum: 10 }
  
  def author_name
    user&.full_name || "Anonymous"
  end
end
```

### Routing

```ruby
# config/routes.rb
Rails.application.routes.draw do
  root 'home#index'
  
  resources :users do
    resources :posts, only: [:index, :show]
  end
  
  resources :posts do
    resources :comments, only: [:create, :destroy]
  end
  
  namespace :admin do
    resources :users
    resources :posts
  end
  
  # API routes
  namespace :api do
    namespace :v1 do
      resources :users, only: [:index, :show, :create, :update, :destroy]
      resources :posts, only: [:index, :show, :create, :update, :destroy]
    end
  end
end
```

## Sinatra Framework

### Basic Sinatra App

```ruby
# app.rb
require 'sinatra'
require 'json'
require 'sqlite3'

class BlogApp < Sinatra::Base
  configure do
    set :public_folder, 'public'
    set :views, 'views'
  end
  
  get '/' do
    @posts = Post.all.order(created_at: :desc)
    erb :index
  end
  
  get '/posts/new' do
    erb :new_post
  end
  
  post '/posts' do
    Post.create(title: params[:title], content: params[:content])
    redirect '/'
  end
  
  get '/posts/:id' do
    @post = Post.find(params[:id])
    erb :show_post
  end
  
  get '/api/posts' do
    content_type :json
    Post.all.to_json
  end
  
  post '/api/posts' do
    content_type :json
    post = Post.create(
      title: params[:title],
      content: params[:content]
    )
    post.to_json
  end
  
  not_found do
    status 404
    "Page not found"
  end
end

# models/post.rb
class Post
  def self.all
    # Simulate database query
    [
      { id: 1, title: "First Post", content: "This is my first post", created_at: Time.now },
      { id: 2, title: "Second Post", content: "This is my second post", created_at: Time.now }
    ]
  end
  
  def self.find(id)
    all.find { |post| post[:id] == id.to_i }
  end
  
  def self.create(params)
    new_post = {
      id: all.length + 1,
      title: params[:title],
      content: params[:content],
      created_at: Time.now
    }
    all << new_post
    new_post
  end
end

# views/index.erb
<!DOCTYPE html>
<html>
<head>
  <title>My Blog</title>
  <link rel="stylesheet" href="/style.css">
</head>
<body>
  <header>
    <h1>My Blog</h1>
    <nav>
      <a href="/">Home</a>
      <a href="/posts/new">New Post</a>
    </nav>
  </header>
  
  <main>
    <% @posts.each do |post| %>
      <article class="post">
        <h2><a href="/posts/<%= post[:id] %>"><%= post[:title] %></a></h2>
        <p><%= post[:content] %></p>
        <small><%= post[:created_at].strftime("%B %d, %Y") %></small>
      </article>
    <% end %>
  </main>
</body>
</html>

# views/new_post.erb
<!DOCTYPE html>
<html>
<head>
  <title>New Post - My Blog</title>
  <link rel="stylesheet" href="/style.css">
</head>
<body>
  <header>
    <h1>My Blog</h1>
    <nav>
      <a href="/">Home</a>
      <a href="/posts/new">New Post</a>
    </nav>
  </header>
  
  <main>
    <h2>New Post</h2>
    <form action="/posts" method="post">
      <div>
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
      </div>
      <div>
        <label for="content">Content:</label>
        <textarea id="content" name="content" rows="10" required></textarea>
      </div>
      <div>
        <button type="submit">Create Post</button>
      </div>
    </form>
  </main>
</body>
</html>

# config.ru
require './app'
run BlogApp

# Run with: rackup config.ru
```

## API Development

### Grape Gem for APIs

```ruby
# Gemfile
source 'https://rubygems.org'
gem 'grape'
gem 'grape-entity'
gem 'grape-swagger'

# app/api/v1/base.rb
module API
  module V1
    class Base < Grape::API
      version 'v1', using: :path
      format :json
      
      # Global error handling
      rescue_from :all do |e|
        error!({ error: e.message }, 500)
      end
      
      # Authentication
      before do
        authenticate! unless request.path == '/api/v1/status'
      end
      
      helpers do
        def authenticate!
          token = request.headers['Authorization']
          error!('Unauthorized', 401) unless valid_token?(token)
        end
        
        def valid_token?(token)
          # Implement your token validation logic
          token == 'valid_token'
        end
      end
    end
  end
end

# app/api/v1/users.rb
module API
  module V1
    class Users < Base
      resource :users do
        desc 'Get all users'
        get do
          User.all
        end
        
        desc 'Get user by ID'
        params do
          requires :id, type: Integer, desc: 'User ID'
        end
        get ':id' do
          User.find(params[:id])
        end
        
        desc 'Create user'
        params do
          requires :name, type: String, desc: 'User name'
          requires :email, type: String, desc: 'User email'
        end
        post do
          User.create!(declared(params))
        end
        
        desc 'Update user'
        params do
          requires :id, type: Integer, desc: 'User ID'
          optional :name, type: String, desc: 'User name'
          optional :email, type: String, desc: 'User email'
        end
        put ':id' do
          user = User.find(params[:id])
          user.update!(declared(params.except(:id)))
          user
        end
        
        desc 'Delete user'
        params do
          requires :id, type: Integer, desc: 'User ID'
        end
        delete ':id' do
          User.find(params[:id]).destroy!
          { message: 'User deleted successfully' }
        end
      end
    end
  end
end

# app/api/v1.rb
module API
  class V1 < Grape::API
    mount Users
    mount Posts
  end
end

# app/api.rb
class API < Grape::API
  prefix 'api'
  format :json
  mount V1
end

# config.ru
require './app/api'
run API
```

### JSON API with Rails

```ruby
# app/controllers/api/v1/users_controller.rb
class Api::V1::UsersController < ApplicationController
  before_action :set_user, only: [:show, :update, :destroy]
  
  def index
    @users = User.all
    render json: @users
  end
  
  def show
    render json: @user
  end
  
  def create
    @user = User.new(user_params)
    
    if @user.save
      render json: @user, status: :created
    else
      render json: @user.errors, status: :unprocessable_entity
    end
  end
  
  def update
    if @user.update(user_params)
      render json: @user
    else
      render json: @user.errors, status: :unprocessable_entity
    end
  end
  
  def destroy
    @user.destroy
    head :no_content
  end
  
  private
  
  def set_user
    @user = User.find(params[:id])
  end
  
  def user_params
    params.require(:user).permit(:name, :email, :first_name, :last_name)
  end
end

# app/controllers/application_controller.rb
class ApplicationController < ActionController::API
  rescue_from ActiveRecord::RecordNotFound, with: :not_found
  rescue_from ActiveRecord::RecordInvalid, with: :unprocessable_entity
  
  private
  
  def not_found(exception)
    render json: { error: exception.message }, status: :not_found
  end
  
  def unprocessable_entity(exception)
    render json: { error: exception.message }, status: :unprocessable_entity
  end
end

# spec/requests/api/v1/users_spec.rb
require 'rails_helper'

RSpec.describe 'Api::V1::Users', type: :request do
  describe 'GET /api/v1/users' do
    it 'returns a successful response' do
      get '/api/v1/users'
      
      expect(response).to have_http_status(:ok)
      expect(response.content_type).to eq('application/json; charset=utf-8')
    end
  end
  
  describe 'POST /api/v1/users' do
    let(:valid_attributes) do
      {
        name: 'John Doe',
        email: 'john@example.com'
      }
    end
    
    it 'creates a new user' do
      post '/api/v1/users', params: { user: valid_attributes }
      
      expect(response).to have_http_status(:created)
      expect(response.content_type).to eq('application/json; charset=utf-8')
    end
  end
end
```

## Frontend Integration

### Hotwire (Rails 7+)

```ruby
# app/controllers/posts_controller.rb
class PostsController < ApplicationController
  def create
    @post = Post.new(post_params)
    
    if @post.save
      Turbo::StreamsChannel.broadcast_replace(
        @post,
        target: "post_#{@post.id}",
        partial: "posts/post",
        locals: { post: @post }
      )
    end
  end
  
  private
  
  def post_params
    params.require(:post).permit(:title, :content)
  end
end

# app/views/posts/_post.html.erb
<div id="post_<%= post.id %>" class="post">
  <h2><%= post.title %></h2>
  <p><%= post.content %></p>
  <small>Created: <%= post.created_at.strftime("%B %d, %Y") %></small>
</div>

# app/views/posts/index.html.erb
<div id="posts">
  <%= render @posts %>
</div>

<%= turbo_frame_tag "new_post", src: new_post_path, target: "_top" do %>
  <%= render 'form', post: @post %>
<% end %>
```

### JavaScript Integration

```ruby
# app/controllers/api_controller.rb
class ApiController < ApplicationController
  def search
    query = params[:q]
    results = if query.present?
      Post.where("title ILIKE ?", "%#{query}%")
           .or(Post.where("content ILIKE ?", "%#{query}%"))
    else
      Post.none
    end
    
    render json: results
  end
end

# app/assets/javascripts/application.js
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('search');
  const resultsContainer = document.getElementById('results');
  
  if (searchInput) {
    let timeout;
    
    searchInput.addEventListener('input', function() {
      clearTimeout(timeout);
      
      timeout = setTimeout(function() {
        const query = searchInput.value.trim();
        
        if (query.length > 2) {
          fetch(`/api/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
              displayResults(data);
            })
            .catch(error => {
              console.error('Search error:', error);
            });
        } else {
          resultsContainer.innerHTML = '';
        }
      }, 300);
    });
  }
  
  function displayResults(results) {
    const html = results.map(post => `
      <div class="result">
        <h3>${post.title}</h3>
        <p>${post.content.substring(0, 100)}...</p>
      </div>
    `).join('');
    
    resultsContainer.innerHTML = html;
  }
});
```

## Testing Web Applications

### RSpec for Rails Testing

```ruby
# spec/models/user_spec.rb
require 'rails_helper'

RSpec.describe User, type: :model do
  it 'has a valid factory' do
    expect(build(:user)).to be_valid
  end
  
  it 'requires a name' do
    user = build(:user, name: nil)
    expect(user).not_to be_valid
  end
  
  it 'requires a unique email' do
    user = create(:user, email: 'test@example.com')
    new_user = build(:user, email: 'test@example.com')
    expect(new_user).not_to be_valid
  end
  
  it 'returns the full name' do
    user = build(:user, first_name: 'John', last_name: 'Doe')
    expect(user.full_name).to eq('John Doe')
  end
  
  it 'has many posts' do
    user = create(:user)
    post1 = create(:post, user: user)
    post2 = create(:post, user: user)
    
    expect(user.posts).to include(post1, post2)
  end
end

# spec/requests/users_spec.rb
require 'rails_helper'

RSpec.describe 'Users', type: :request do
  describe 'GET /users' do
    it 'returns a successful response' do
      get users_path
      
      expect(response).to have_http_status(:ok)
    end
  end
  
  describe 'POST /users' do
    context 'with valid parameters' do
      it 'creates a new user' do
        expect {
          post users_path, params: { user: attributes_for(:user) }
        }.to change(User, :count).by(1)
      end
    end
    
    context 'with invalid parameters' do
      it 'does not create a user' do
        expect {
          post users_path, params: { user: { name: '' } }
        }.not_to change(User, :count)
      end
    end
  end
end

# spec/system/user_management_spec.rb
require 'rails_helper'

RSpec.describe 'User Management', type: :system do
  let(:user) { create(:user) }
  
  before do
    login_as(user)
  end
  
  scenario 'Creating a new user' do
    visit users_path
    click_link 'New User'
    
    fill_in 'Name', with: 'New User'
    fill_in 'Email', with: 'newuser@example.com'
    click_button 'Create User'
    
    expect(page).to have_content('User was successfully created.')
    expect(page).to have_content('New User')
  end
  
  scenario 'Editing an existing user' do
    visit users_path
    click_link 'Edit', match: :first
    
    fill_in 'Name', with: 'Updated User'
    click_button 'Update User'
    
    expect(page).to have_content('User was successfully updated.')
    expect(page).to have_content('Updated User')
  end
  
  scenario 'Deleting a user' do
    visit users_path
    click_link 'Destroy', match: :first
    
    expect(page).to have_content('User was successfully deleted.')
  end
end
```

## Performance Optimization

### Caching Strategies

```ruby
# app/controllers/posts_controller.rb
class PostsController < ApplicationController
  def index
    @posts = Rails.cache.fetch('posts_index', expires_in: 5.minutes) do
      Post.includes(:user, :comments).published.recent.limit(10)
    end
  end
  
  def show
    @post = Rails.cache.fetch("post_#{params[:id]}", expires_in: 1.hour) do
      Post.includes(:user, :comments).find(params[:id])
    end
  end
end

# app/models/post.rb
class Post < ApplicationRecord
  def self.cached_popular
    Rails.cache.fetch('popular_posts', expires_in: 1.hour) do
      published.joins(:comments)
            .group('posts.id')
            .order('COUNT(comments.id) DESC')
            .limit(5)
    end
  end
  
  def cached_comment_count
    Rails.cache.fetch("post_#{id}_comment_count", expires_in: 5.minutes) do
      comments.count
    end
end
```

### Database Optimization

```ruby
# app/models/user.rb
class User < ApplicationRecord
  # Add database indexes
  # add_index :users, :email, unique: true
  # add_index :users, :created_at
  
  # Use select instead of loading all columns
  def self.with_posts_count
    select('users.*, COUNT(posts.id) as posts_count')
      .left_joins(:posts)
      .group('users.id')
  end
  
  # Use pluck for single values
  def self.total_count
    pluck(:id).count
  end
  
  # Use find_each for large datasets
  def self.process_all_in_batches(batch_size: 1000)
    find_each(batch_size: batch_size) do |batch|
      yield batch
    end
  end
end
```

## Best Practices

### 1. Security

```ruby
# Strong parameters
class UsersController < ApplicationController
  def user_params
    params.require(:user).permit(:name, :email)
  end
end

# CSRF protection
class ApplicationController < ActionController::Base
  protect_from_forgery with: :exception
end

# Input validation
class User < ApplicationRecord
  validates :email, format: { with: URI::MailTo::EMAIL_REGEXP }
end
```

### 2. Error Handling

```ruby
# Custom error pages
class ErrorsController < ApplicationController
  def not_found
    render status: :not_found
  end
  
  def internal_server_error
    render status: :internal_server_error
  end
end

# Exception handling
class ApplicationController < ActionController::Base
  rescue_from ActiveRecord::RecordNotFound, with: :not_found
  rescue_from ActiveRecord::RecordInvalid, with: :unprocessable_entity
  
  private
  
  def not_found
    render file: Rails.root.join('public', '404.html'), status: :not_found
  end
end
```

### 3. Code Organization

```ruby
# Use service objects
class UserRegistrationService
  def initialize(user_params)
    @user_params = user_params
  end
  
  def call
    user = User.new(@user_params)
    
    if user.valid?
      user.save!
      send_welcome_email(user)
      Success.new(user)
    else
      Failure.new(user.errors)
    end
  end
  
  private
  
  def send_welcome_email(user)
    UserMailer.welcome(user).deliver_later
  end
end

# Use decorators/presenters
class UserPresenter
  def initialize(user)
    @user = user
  end
  
  def display_name
    @user.full_name || 'Anonymous'
  end
  
  def registration_date
    @user.created_at.strftime('%B %d, %Y')
  end
end
```

## Practice Exercises

### Exercise 1: Blog API
Create a complete blog API with:
- CRUD operations for posts and comments
- Authentication and authorization
- Pagination and filtering
- Rate limiting
- API documentation

### Exercise 2: E-commerce Site
Build an e-commerce site with:
- Product catalog
- Shopping cart
- Order processing
- Payment integration
- Admin dashboard

### Exercise 3: Social Media Platform
Implement a social media platform with:
- User profiles
- Posts and comments
- Follow system
- Real-time updates
- File uploads

### Exercise 4: Project Management Tool
Create a project management tool with:
- Projects and tasks
- Team collaboration
- File sharing
- Notifications
- Reporting dashboard

---

**Ready to explore more advanced Ruby topics? Let's continue! 🌐**
