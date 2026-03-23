# Ruby Guide Images

This directory contains images, screenshots, and visual assets that support the Ruby Programming Guide documentation.

## Image Categories

### Ruby Language
- **ruby-logo.png**: Official Ruby programming language logo
- **ruby-version-history.png**: Ruby version timeline and major features
- **syntax-highlighting.png**: Ruby code with syntax highlighting examples
- **irb-console.png**: Interactive Ruby console screenshot

### Ruby on Rails
- **rails-logo.png**: Ruby on Rails framework logo
- **rails-console.png**: Rails console demonstration
- **rails-directory-structure.png**: Rails application file organization
- **rails-generator-output.png**: Rails generator command output
- **rails-error-pages.png**: Common Rails error pages

### Development Environment
- **vscode-ruby.png**: VS Code with Ruby extensions
- **ruby-mine.png**: JetBrains RubyMine IDE
- **sublime-text.png**: Sublime Text Ruby setup
- **terminal-setup.png**: Terminal with Ruby environment
- **git-bash.png**: Git Bash with Ruby commands

### Code Examples
- **class-definition.png**: Ruby class definition screenshot
- **method-definitions.png**: Various Ruby method types
- **block-examples.png**: Ruby blocks and yield usage
- **module-mixins.png**: Module and mixin examples
- **exception-handling.png**: Ruby exception handling patterns

### Database and ORM
- **active-record-query.png**: ActiveRecord query examples
- **database-schema.png**: Database schema visualization
- **migration-output.png**: Rails migration command output
- **model-associations.png**: Model relationship diagrams
- **sql-output.png**: SQL query results

### Testing
- **rspec-output.png**: RSpec test runner output
- **test-failure.png**: Test failure examples
- **coverage-report.png**: Code coverage visualization
- **factory-bot.png**: FactoryBot usage examples
- **cucumber-scenarios.png**: Cucumber feature examples

### Web Development
- **http-requests.png**: HTTP request examples
- **api-response.png**: JSON API response format
- **authentication-form.png**: User authentication interface
- **error-pages.png**: Custom error page designs
- **responsive-design.png**: Mobile-responsive layouts

### Deployment
- **heroku-dashboard.png**: Heroku application dashboard
- **docker-container.png**: Docker container setup
- **aws-console.png**: AWS management console
- **nginx-config.png**: Nginx configuration example
- **ssl-certificate.png**: SSL certificate information

## Image Standards

### File Formats
- **PNG**: Best for screenshots and diagrams
- **JPEG**: For photographs and complex images
- **SVG**: For logos and scalable graphics
- **GIF**: For animated demonstrations

### Sizing Guidelines
- **Screenshots**: Maximum width 1200px
- **Logos**: Vector format when possible
- **Diagrams**: High resolution for clarity
- **Thumbnails**: 300x300px for previews

### Quality Standards
- **Resolution**: Minimum 72 DPI for web
- **Compression**: Optimize for web display
- **Clarity**: Text must be readable
- **Consistency**: Maintain visual style consistency

## Usage in Documentation

### Basic Image Inclusion
```markdown
![Ruby Logo](../assets/images/ruby-logo.png)
```

### With Size Specification
```markdown
![Rails Console](../assets/images/rails-console.png){:width="600px"}
```

### With Caption
```markdown
![Ruby Syntax](../assets/images/syntax-highlighting.png)
*Ruby syntax highlighting example*
```

### Responsive Images
```markdown
![Responsive Design](../assets/images/responsive-design.png)
{:class="img-responsive"}
```

## Creating New Images

### Screenshot Guidelines
1. **Use consistent window size**: 1920x1080 recommended
2. **Hide personal information**: Remove sensitive data
3. **Use high contrast**: Ensure text is readable
4. **Crop appropriately**: Focus on relevant content
5. **Add annotations**: Use arrows and text to highlight features

### Image Editing Tools
- **Snagit**: Professional screenshot tool
- **Greenshot**: Free screenshot software
- **GIMP**: Free image editing software
- **ImageOptim**: Image compression tool
- **TinyPNG**: Online image optimizer

### Naming Conventions
- Use descriptive names: `rails-console-setup.png`
- Include version numbers: `ruby-3.2-logo.png`
- Use hyphens instead of spaces
- Keep names reasonably short

## Image Organization

### Directory Structure
```
images/
├── ruby/                 # Ruby language images
├── rails/               # Rails framework images
├── development/         # Development environment
├── code-examples/       # Code demonstration images
├── database/           # Database and ORM images
├── testing/            # Testing framework images
├── web-development/    # Web development images
├── deployment/         # Deployment and infrastructure
└── logos/              # Technology logos
```

### File Categories
- **Screenshots**: Application interfaces and command outputs
- **Diagrams**: Technical illustrations and flowcharts
- **Logos**: Technology and framework logos
- **Examples**: Code examples and demonstrations
- **Infographics**: Informational graphics and charts

## Accessibility

### Alt Text Guidelines
- Be descriptive but concise
- Explain the purpose of the image
- Include relevant details
- Avoid "image of" or "picture of"

### Example Alt Text
```markdown
![Rails Console](../assets/images/rails-console.png)
*Rails console showing database queries and model interactions*
```

### Color Considerations
- Ensure sufficient contrast
- Test with color blindness simulators
- Provide text alternatives for color-coded information
- Use accessible color schemes

## Maintenance

### Regular Reviews
- Check for outdated screenshots
- Update with new software versions
- Refresh styling and formatting
- Remove unused images

### Quality Assurance
- Verify all images display correctly
- Check for broken image links
- Test on different devices and browsers
- Ensure file sizes are optimized

## Contributing

### Adding New Images
1. Check if similar images already exist
2. Choose appropriate subdirectory
3. Follow naming conventions
4. Optimize for web display
5. Update this README file
6. Add image references in documentation

### Image Requirements
- Must be relevant to Ruby programming
- Should enhance understanding of concepts
- Must be properly licensed or original
- Should follow technical standards

### Review Process
1. Ensure image quality and clarity
2. Verify file naming conventions
3. Check accessibility compliance
4. Test in documentation context
5. Update related documentation

## Technical Specifications

### Image Optimization
- **PNG**: Use for screenshots and diagrams
- **JPEG**: Use for photographs
- **WebP**: Modern format for web images
- **AVIF**: Next-generation image format

### Compression Settings
- **Quality**: 80-90% for JPEG
- **Compression**: Lossless for PNG
- **Size**: Keep under 500KB when possible
- **Dimensions**: Responsive scaling

### Color Profiles
- **sRGB**: Standard for web images
- **Display P3**: For high-quality displays
- **Grayscale**: For black and white images
- **CMYK**: For print materials

## Resources

### Image Creation Tools
- [Snagit](https://www.techsmith.com/screen-capture.html)
- [Greenshot](https://getgreenshot.org/)
- [GIMP](https://www.gimp.org/)
- [Canva](https://www.canva.com/)
- [Figma](https://www.figma.com/)

### Optimization Tools
- [TinyPNG](https://tinypng.com/)
- [ImageOptim](https://imageoptim.com/)
- [Squoosh](https://squoosh.app/)
- [Kraken.io](https://kraken.io/)

### Accessibility Resources
- [WebAIM](https://webaim.org/)
- [A11Y Project](https://www.a11yproject.com/)
- [Color Contrast Checker](https://webaim.org/resources/contrastchecker/)
- [Alt Text Decision Tree](https://github.com/alt-text-decision-tree/alt-text-decision-tree)

### Stock Image Resources
- [Unsplash](https://unsplash.com/)
- [Pexels](https://www.pexels.com/)
- [Pixabay](https://pixabay.com/)
- [Ruby-specific resources](https://www.ruby-lang.org/en/documentation/)
