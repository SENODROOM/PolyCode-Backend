<?php
/**
 * Resume and Portfolio Development
 * 
 * This file demonstrates resume writing, portfolio creation,
 * personal branding, and professional presentation strategies.
 */

// Resume Builder
class ResumeBuilder
{
    private array $sections = [];
    private array $personalInfo = [];
    private array $skills = [];
    private array $experience = [];
    private array $education = [];
    private array $projects = [];
    private array $certifications = [];
    
    public function __construct()
    {
        $this->initializeSections();
    }
    
    /**
     * Initialize resume sections
     */
    private function initializeSections(): void
    {
        $this->sections = [
            'personal_info' => [
                'title' => 'Personal Information',
                'required' => true,
                'order' => 1
            ],
            'summary' => [
                'title' => 'Professional Summary',
                'required' => true,
                'order' => 2
            ],
            'skills' => [
                'title' => 'Technical Skills',
                'required' => true,
                'order' => 3
            ],
            'experience' => [
                'title' => 'Work Experience',
                'required' => true,
                'order' => 4
            ],
            'education' => [
                'title' => 'Education',
                'required' => true,
                'order' => 5
            ],
            'projects' => [
                'title' => 'Projects',
                'required' => false,
                'order' => 6
            ],
            'certifications' => [
                'title' => 'Certifications',
                'required' => false,
                'order' => 7
            ],
            'languages' => [
                'title' => 'Languages',
                'required' => false,
                'order' => 8
            ],
            'interests' => [
                'title' => 'Interests',
                'required' => false,
                'order' => 9
            ]
        ];
    }
    
    /**
     * Set personal information
     */
    public function setPersonalInfo(array $info): void
    {
        $this->personalInfo = array_merge([
            'name' => '',
            'title' => '',
            'email' => '',
            'phone' => '',
            'location' => '',
            'linkedin' => '',
            'github' => '',
            'portfolio' => '',
            'website' => ''
        ], $info);
    }
    
    /**
     * Add skill
     */
    public function addSkill(string $category, array $skills): void
    {
        $this->skills[$category] = $skills;
    }
    
    /**
     * Add work experience
     */
    public function addExperience(array $experience): void
    {
        $this->experience[] = array_merge([
            'company' => '',
            'position' => '',
            'location' => '',
            'start_date' => '',
            'end_date' => '',
            'current' => false,
            'description' => '',
            'achievements' => [],
            'technologies' => []
        ], $experience);
    }
    
    /**
     * Add education
     */
    public function addEducation(array $education): void
    {
        $this->education[] = array_merge([
            'institution' => '',
            'degree' => '',
            'field' => '',
            'location' => '',
            'start_date' => '',
            'end_date' => '',
            'gpa' => '',
            'honors' => []
        ], $education);
    }
    
    /**
     * Add project
     */
    public function addProject(array $project): void
    {
        $this->projects[] = array_merge([
            'name' => '',
            'description' => '',
            'technologies' => [],
            'url' => '',
            'github' => '',
            'start_date' => '',
            'end_date' => '',
            'highlights' => []
        ], $project);
    }
    
    /**
     * Add certification
     */
    public function addCertification(array $certification): void
    {
        $this->certifications[] = array_merge([
            'name' => '',
            'issuer' => '',
            'date' => '',
            'expiry' => '',
            'credential_id' => '',
            'url' => ''
        ], $certification);
    }
    
    /**
     * Generate resume content
     */
    public function generateResume(string $format = 'text'): string
    {
        switch ($format) {
            case 'html':
                return $this->generateHTMLResume();
            case 'markdown':
                return $this->generateMarkdownResume();
            case 'json':
                return $this->generateJSONResume();
            default:
                return $this->generateTextResume();
        }
    }
    
    /**
     * Generate text resume
     */
    private function generateTextResume(): string
    {
        $resume = "";
        
        // Personal Information
        $resume .= $this->personalInfo['name'] . "\n";
        $resume .= $this->personalInfo['title'] . "\n";
        $resume .= $this->personalInfo['email'] . " | " . $this->personalInfo['phone'] . " | " . $this->personalInfo['location'] . "\n";
        
        if ($this->personalInfo['linkedin']) {
            $resume .= "LinkedIn: " . $this->personalInfo['linkedin'] . "\n";
        }
        if ($this->personalInfo['github']) {
            $resume .= "GitHub: " . $this->personalInfo['github'] . "\n";
        }
        if ($this->personalInfo['portfolio']) {
            $resume .= "Portfolio: " . $this->personalInfo['portfolio'] . "\n";
        }
        
        $resume .= "\n" . str_repeat("=", 50) . "\n\n";
        
        // Professional Summary
        $resume .= "PROFESSIONAL SUMMARY\n";
        $resume .= str_repeat("-", 18) . "\n";
        $resume .= "Experienced PHP Developer with X years of expertise in building scalable web applications. ";
        $resume .= "Proficient in modern PHP frameworks, database design, and API development. ";
        $resume .= "Strong problem-solving skills and commitment to writing clean, maintainable code.\n\n";
        
        // Technical Skills
        $resume .= "TECHNICAL SKILLS\n";
        $resume .= str_repeat("-", 15) . "\n";
        
        foreach ($this->skills as $category => $skills) {
            $resume .= strtoupper($category) . ": " . implode(', ', $skills) . "\n";
        }
        $resume .= "\n";
        
        // Work Experience
        $resume .= "WORK EXPERIENCE\n";
        $resume .= str_repeat("-", 14) . "\n";
        
        foreach ($this->experience as $exp) {
            $resume .= $exp['position'] . " - " . $exp['company'] . "\n";
            $resume .= $exp['start_date'] . " - " . ($exp['current'] ? 'Present' : $exp['end_date']) . " | " . $exp['location'] . "\n";
            $resume .= $exp['description'] . "\n";
            
            if (!empty($exp['achievements'])) {
                $resume .= "Key Achievements:\n";
                foreach ($exp['achievements'] as $achievement) {
                    $resume .= "• " . $achievement . "\n";
                }
            }
            
            if (!empty($exp['technologies'])) {
                $resume .= "Technologies: " . implode(', ', $exp['technologies']) . "\n";
            }
            
            $resume .= "\n";
        }
        
        // Education
        $resume .= "EDUCATION\n";
        $resume .= str_repeat("-", 9) . "\n";
        
        foreach ($this->education as $edu) {
            $resume .= $edu['degree'] . " in " . $edu['field'] . "\n";
            $resume .= $edu['institution'] . " | " . $edu['start_date'] . " - " . $edu['end_date'] . "\n";
            
            if ($edu['gpa']) {
                $resume .= "GPA: " . $edu['gpa'] . "\n";
            }
            
            if (!empty($edu['honors'])) {
                $resume .= "Honors: " . implode(', ', $edu['honors']) . "\n";
            }
            
            $resume .= "\n";
        }
        
        // Projects
        if (!empty($this->projects)) {
            $resume .= "PROJECTS\n";
            $resume .= str_repeat("-", 8) . "\n";
            
            foreach ($this->projects as $project) {
                $resume .= $project['name'] . "\n";
                $resume .= $project['description'] . "\n";
                $resume .= "Technologies: " . implode(', ', $project['technologies']) . "\n";
                
                if (!empty($project['highlights'])) {
                    foreach ($project['highlights'] as $highlight) {
                        $resume .= "• " . $highlight . "\n";
                    }
                }
                
                $resume .= "\n";
            }
        }
        
        // Certifications
        if (!empty($this->certifications)) {
            $resume .= "CERTIFICATIONS\n";
            $resume .= str_repeat("-", 14) . "\n";
            
            foreach ($this->certifications as $cert) {
                $resume .= $cert['name'] . " - " . $cert['issuer'] . "\n";
                $resume .= "Issued: " . $cert['date'];
                if ($cert['expiry']) {
                    $resume .= " | Expires: " . $cert['expiry'];
                }
                $resume .= "\n\n";
            }
        }
        
        return $resume;
    }
    
    /**
     * Generate HTML resume
     */
    private function generateHTMLResume(): string
    {
        $html = "<!DOCTYPE html>\n<html>\n<head>\n";
        $html .= "<title>{$this->personalInfo['name']} - Resume</title>\n";
        $html .= "<style>\n";
        $html .= "body { font-family: Arial, sans-serif; line-height: 1.6; margin: 40px; }\n";
        $html .= ".header { text-align: center; margin-bottom: 30px; }\n";
        $html .= ".name { font-size: 2.5em; margin-bottom: 5px; }\n";
        $html .= ".title { font-size: 1.2em; color: #666; margin-bottom: 10px; }\n";
        $html .= ".contact { margin-bottom: 20px; }\n";
        $html .= ".section { margin-bottom: 30px; }\n";
        $html .= ".section-title { font-size: 1.3em; font-weight: bold; border-bottom: 2px solid #333; padding-bottom: 5px; }\n";
        $html .= ".experience-item { margin-bottom: 20px; }\n";
        $html .= ".job-title { font-weight: bold; }\n";
        $html .= ".company { font-style: italic; }\n";
        $html .= ".date { color: #666; }\n";
        $html .= ".skills-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }\n";
        $html .= ".skill-category { font-weight: bold; margin-bottom: 5px; }\n";
        $html .= "</style>\n</head>\n<body>\n";
        
        // Header
        $html .= "<div class=\"header\">\n";
        $html .= "<h1 class=\"name\">{$this->personalInfo['name']}</h1>\n";
        $html .= "<div class=\"title\">{$this->personalInfo['title']}</div>\n";
        $html .= "<div class=\"contact\">\n";
        $html .= "{$this->personalInfo['email']} | {$this->personalInfo['phone']} | {$this->personalInfo['location']}\n";
        if ($this->personalInfo['linkedin']) {
            $html .= "<br>LinkedIn: <a href=\"{$this->personalInfo['linkedin']}\">{$this->personalInfo['linkedin']}</a>\n";
        }
        if ($this->personalInfo['github']) {
            $html .= " | GitHub: <a href=\"{$this->personalInfo['github']}\">{$this->personalInfo['github']}</a>\n";
        }
        if ($this->personalInfo['portfolio']) {
            $html .= " | Portfolio: <a href=\"{$this->personalInfo['portfolio']}\">{$this->personalInfo['portfolio']}</a>\n";
        }
        $html .= "</div>\n</div>\n";
        
        // Professional Summary
        $html .= "<div class=\"section\">\n";
        $html .= "<h2 class=\"section-title\">Professional Summary</h2>\n";
        $html .= "<p>Experienced PHP Developer with expertise in building scalable web applications. ";
        $html .= "Proficient in modern PHP frameworks, database design, and API development. ";
        $html .= "Strong problem-solving skills and commitment to writing clean, maintainable code.</p>\n";
        $html .= "</div>\n";
        
        // Skills
        $html .= "<div class=\"section\">\n";
        $html .= "<h2 class=\"section-title\">Technical Skills</h2>\n";
        $html .= "<div class=\"skills-grid\">\n";
        foreach ($this->skills as $category => $skills) {
            $html .= "<div>\n";
            $html .= "<div class=\"skill-category\">" . ucwords($category) . "</div>\n";
            $html .= "<div>" . implode(', ', $skills) . "</div>\n";
            $html .= "</div>\n";
        }
        $html .= "</div>\n</div>\n";
        
        // Experience
        $html .= "<div class=\"section\">\n";
        $html .= "<h2 class=\"section-title\">Work Experience</h2>\n";
        foreach ($this->experience as $exp) {
            $html .= "<div class=\"experience-item\">\n";
            $html .= "<div class=\"job-title\">{$exp['position']}</div>\n";
            $html .= "<div class=\"company\">{$exp['company']} | {$exp['location']}</div>\n";
            $html .= "<div class=\"date\">" . $exp['start_date'] . " - " . ($exp['current'] ? 'Present' : $exp['end_date']) . "</div>\n";
            $html .= "<p>{$exp['description']}</p>\n";
            
            if (!empty($exp['achievements'])) {
                $html .= "<ul>\n";
                foreach ($exp['achievements'] as $achievement) {
                    $html .= "<li>{$achievement}</li>\n";
                }
                $html .= "</ul>\n";
            }
            
            if (!empty($exp['technologies'])) {
                $html .= "<div><strong>Technologies:</strong> " . implode(', ', $exp['technologies']) . "</div>\n";
            }
            
            $html .= "</div>\n";
        }
        $html .= "</div>\n";
        
        // Education
        $html .= "<div class=\"section\">\n";
        $html .= "<h2 class=\"section-title\">Education</h2>\n";
        foreach ($this->education as $edu) {
            $html .= "<div>\n";
            $html .= "<div class=\"job-title\">{$edu['degree']} in {$edu['field']}</div>\n";
            $html .= "<div class=\"company\">{$edu['institution']}</div>\n";
            $html .= "<div class=\"date\">" . $edu['start_date'] . " - " . $edu['end_date'] . "</div>\n";
            
            if ($edu['gpa']) {
                $html .= "<div>GPA: {$edu['gpa']}</div>\n";
            }
            
            $html .= "</div>\n";
        }
        $html .= "</div>\n";
        
        $html .= "</body>\n</html>";
        
        return $html;
    }
    
    /**
     * Generate markdown resume
     */
    private function generateMarkdownResume(): string
    {
        $md = "# {$this->personalInfo['name']}\n\n";
        $md .= "**{$this->personalInfo['title']}**\n\n";
        $md .= "{$this->personalInfo['email']} | {$this->personalInfo['phone']} | {$this->personalInfo['location']}\n\n";
        
        if ($this->personalInfo['linkedin']) {
            $md .= "[LinkedIn]({$this->personalInfo['linkedin']}) | ";
        }
        if ($this->personalInfo['github']) {
            $md .= "[GitHub]({$this->personalInfo['github']}) | ";
        }
        if ($this->personalInfo['portfolio']) {
            $md .= "[Portfolio]({$this->personalInfo['portfolio']})";
        }
        
        $md .= "\n\n---\n\n";
        
        // Professional Summary
        $md .= "## Professional Summary\n\n";
        $md .= "Experienced PHP Developer with expertise in building scalable web applications. ";
        $md .= "Proficient in modern PHP frameworks, database design, and API development. ";
        $md .= "Strong problem-solving skills and commitment to writing clean, maintainable code.\n\n";
        
        // Skills
        $md .= "## Technical Skills\n\n";
        foreach ($this->skills as $category => $skills) {
            $md .= "**" . ucwords($category) . ":** " . implode(', ', $skills) . "\n\n";
        }
        
        // Experience
        $md .= "## Work Experience\n\n";
        foreach ($this->experience as $exp) {
            $md .= "### {$exp['position']} - {$exp['company']}\n";
            $md .= "*{$exp['start_date']} - " . ($exp['current'] ? 'Present' : $exp['end_date']) . " | {$exp['location']}*\n\n";
            $md .= "{$exp['description']}\n\n";
            
            if (!empty($exp['achievements'])) {
                foreach ($exp['achievements'] as $achievement) {
                    $md .= "- $achievement\n";
                }
                $md .= "\n";
            }
            
            if (!empty($exp['technologies'])) {
                $md .= "**Technologies:** " . implode(', ', $exp['technologies']) . "\n\n";
            }
        }
        
        // Education
        $md .= "## Education\n\n";
        foreach ($this->education as $edu) {
            $md .= "### {$edu['degree']} in {$edu['field']}\n";
            $md .= "*{$edu['institution']} | {$edu['start_date']} - {$edu['end_date']}*\n\n";
            
            if ($edu['gpa']) {
                $md .= "**GPA:** {$edu['gpa']}\n\n";
            }
        }
        
        return $md;
    }
    
    /**
     * Generate JSON resume
     */
    private function generateJSONResume(): string
    {
        $resume = [
            'personal_info' => $this->personalInfo,
            'summary' => 'Experienced PHP Developer with expertise in building scalable web applications.',
            'skills' => $this->skills,
            'experience' => $this->experience,
            'education' => $this->education,
            'projects' => $this->projects,
            'certifications' => $this->certifications
        ];
        
        return json_encode($resume, JSON_PRETTY_PRINT);
    }
    
    /**
     * Validate resume completeness
     */
    public function validateResume(): array
    {
        $errors = [];
        $warnings = [];
        $missing = [];
        
        // Check required sections
        foreach ($this->sections as $section => $config) {
            if ($config['required']) {
                $sectionData = $this->getSectionData($section);
                if (empty($sectionData)) {
                    $errors[] = "Missing required section: {$config['title']}";
                    $missing[] = $section;
                }
            }
        }
        
        // Check personal info completeness
        if (empty($this->personalInfo['name'])) {
            $errors[] = 'Name is required';
        }
        if (empty($this->personalInfo['email'])) {
            $errors[] = 'Email is required';
        }
        if (empty($this->personalInfo['phone'])) {
            $warnings[] = 'Phone number is recommended';
        }
        
        // Check experience
        if (empty($this->experience)) {
            $errors[] = 'At least one work experience is required';
        }
        
        // Check skills
        if (empty($this->skills)) {
            $errors[] = 'Technical skills are required';
        }
        
        // Check education
        if (empty($this->education)) {
            $errors[] = 'Education information is required';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'missing_sections' => $missing,
            'completeness_score' => $this->calculateCompletenessScore()
        ];
    }
    
    /**
     * Get section data
     */
    private function getSectionData(string $section): array
    {
        switch ($section) {
            case 'personal_info':
                return $this->personalInfo;
            case 'skills':
                return $this->skills;
            case 'experience':
                return $this->experience;
            case 'education':
                return $this->education;
            case 'projects':
                return $this->projects;
            case 'certifications':
                return $this->certifications;
            default:
                return [];
        }
    }
    
    /**
     * Calculate completeness score
     */
    private function calculateCompletenessScore(): int
    {
        $totalSections = count($this->sections);
        $completedSections = 0;
        
        foreach ($this->sections as $section => $config) {
            $data = $this->getSectionData($section);
            if (!empty($data)) {
                $completedSections++;
            }
        }
        
        return (int) (($completedSections / $totalSections) * 100);
    }
}

// Portfolio Builder
class PortfolioBuilder
{
    private array $projects = [];
    private array $skills = [];
    private array $about = [];
    private array $contact = [];
    private array $social = [];
    private string $theme = 'modern';
    
    public function __construct()
    {
        $this->initializeDefaultContent();
    }
    
    /**
     * Initialize default content
     */
    private function initializeDefaultContent(): void
    {
        $this->about = [
            'title' => 'About Me',
            'content' => '',
            'skills_highlight' => [],
            'background_image' => ''
        ];
        
        $this->contact = [
            'email' => '',
            'phone' => '',
            'location' => '',
            'available_for' => ['freelance', 'full-time', 'consulting']
        ];
        
        $this->social = [
            'github' => '',
            'linkedin' => '',
            'twitter' => '',
            'instagram' => '',
            'website' => ''
        ];
    }
    
    /**
     * Add project to portfolio
     */
    public function addProject(array $project): void
    {
        $this->projects[] = array_merge([
            'title' => '',
            'description' => '',
            'technologies' => [],
            'features' => [],
            'images' => [],
            'live_url' => '',
            'github_url' => '',
            'category' => '',
            'completion_date' => '',
            'client' => '',
            'role' => '',
            'challenges' => [],
            'solutions' => []
        ], $project);
    }
    
    /**
     * Set about section
     */
    public function setAbout(array $about): void
    {
        $this->about = array_merge($this->about, $about);
    }
    
    /**
     * Set contact information
     */
    public function setContact(array $contact): void
    {
        $this->contact = array_merge($this->contact, $contact);
    }
    
    /**
     * Set social links
     */
    public function setSocial(array $social): void
    {
        $this->social = array_merge($this->social, $social);
    }
    
    /**
     * Set theme
     */
    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
    }
    
    /**
     * Generate portfolio website
     */
    public function generatePortfolio(): string
    {
        $html = $this->generatePortfolioHTML();
        
        return $html;
    }
    
    /**
     * Generate portfolio HTML
     */
    private function generatePortfolioHTML(): string
    {
        $html = "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n";
        $html .= "<meta charset=\"UTF-8\">\n";
        $html .= "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
        $html .= "<title>Portfolio - PHP Developer</title>\n";
        $html .= $this->getPortfolioCSS();
        $html .= "</head>\n<body>\n";
        
        // Navigation
        $html .= $this->generateNavigation();
        
        // Hero Section
        $html .= $this->generateHeroSection();
        
        // About Section
        $html .= $this->generateAboutSection();
        
        // Skills Section
        $html .= $this->generateSkillsSection();
        
        // Projects Section
        $html .= $this->generateProjectsSection();
        
        // Contact Section
        $html .= $this->generateContactSection();
        
        // Footer
        $html .= $this->generateFooter();
        
        $html .= $this->getPortfolioJS();
        $html .= "</body>\n</html>";
        
        return $html;
    }
    
    /**
     * Get portfolio CSS
     */
    private function getPortfolioCSS(): string
    {
        return "<style>\n" . file_get_contents(__DIR__ . '/portfolio-styles.css') . "\n</style>\n";
    }
    
    /**
     * Generate navigation
     */
    private function generateNavigation(): string
    {
        $nav = "<nav class=\"navbar\">\n";
        $nav .= "<div class=\"nav-container\">\n";
        $nav .= "<div class=\"nav-logo\">Portfolio</div>\n";
        $nav .= "<ul class=\"nav-menu\">\n";
        $nav .= "<li><a href=\"#home\">Home</a></li>\n";
        $nav .= "<li><a href=\"#about\">About</a></li>\n";
        $nav .= "<li><a href=\"#skills\">Skills</a></li>\n";
        $nav .= "<li><a href=\"#projects\">Projects</a></li>\n";
        $nav .= "<li><a href=\"#contact\">Contact</a></li>\n";
        $nav .= "</ul>\n";
        $nav .= "<div class=\"nav-toggle\">☰</div>\n";
        $nav .= "</div>\n";
        $nav .= "</nav>\n";
        
        return $nav;
    }
    
    /**
     * Generate hero section
     */
    private function generateHeroSection(): string
    {
        $hero = "<section id=\"home\" class=\"hero\">\n";
        $hero .= "<div class=\"hero-content\">\n";
        $hero .= "<h1>PHP Developer</h1>\n";
        $hero .= "<p>Building scalable web applications with modern PHP</p>\n";
        $hero .= "<div class=\"hero-buttons\">\n";
        $hero .= "<a href=\"#projects\" class=\"btn btn-primary\">View Projects</a>\n";
        $hero .= "<a href=\"#contact\" class=\"btn btn-secondary\">Contact Me</a>\n";
        $hero .= "</div>\n";
        $hero .= "</div>\n";
        $hero .= "</section>\n";
        
        return $hero;
    }
    
    /**
     * Generate about section
     */
    private function generateAboutSection(): string
    {
        $about = "<section id=\"about\" class=\"about\">\n";
        $about .= "<div class=\"container\">\n";
        $about .= "<h2>About Me</h2>\n";
        $about .= "<div class=\"about-content\">\n";
        $about .= "<div class=\"about-text\">\n";
        $about .= "<p>{$this->about['content']}</p>\n";
        $about .= "</div>\n";
        $about .= "<div class=\"about-skills\">\n";
        
        foreach ($this->about['skills_highlight'] as $skill) {
            $about .= "<span class=\"skill-tag\">$skill</span>\n";
        }
        
        $about .= "</div>\n";
        $about .= "</div>\n";
        $about .= "</div>\n";
        $about .= "</section>\n";
        
        return $about;
    }
    
    /**
     * Generate skills section
     */
    private function generateSkillsSection(): string
    {
        $skills = "<section id=\"skills\" class=\"skills\">\n";
        $skills .= "<div class=\"container\">\n";
        $skills .= "<h2>Technical Skills</h2>\n";
        $skills .= "<div class=\"skills-grid\">\n";
        
        foreach ($this->skills as $category => $skillList) {
            $skills .= "<div class=\"skill-category\">\n";
            $skills .= "<h3>" . ucwords($category) . "</h3>\n";
            $skills .= "<div class=\"skill-list\">\n";
            
            foreach ($skillList as $skill) {
                $skills .= "<div class=\"skill-item\">\n";
                $skills .= "<span class=\"skill-name\">$skill</span>\n";
                $skills .= "<div class=\"skill-bar\">\n";
                $skills .= "<div class=\"skill-progress\" style=\"width: 85%;\"></div>\n";
                $skills .= "</div>\n";
                $skills .= "</div>\n";
            }
            
            $skills .= "</div>\n";
            $skills .= "</div>\n";
        }
        
        $skills .= "</div>\n";
        $skills .= "</div>\n";
        $skills .= "</section>\n";
        
        return $skills;
    }
    
    /**
     * Generate projects section
     */
    private function generateProjectsSection(): string
    {
        $projects = "<section id=\"projects\" class=\"projects\">\n";
        $projects .= "<div class=\"container\">\n";
        $projects .= "<h2>Featured Projects</h2>\n";
        $projects .= "<div class=\"projects-grid\">\n";
        
        foreach ($this->projects as $project) {
            $projects .= "<div class=\"project-card\">\n";
            $projects .= "<div class=\"project-image\">\n";
            $projects .= "<img src=\"{$project['images'][0] ?? '/placeholder.jpg'}\" alt=\"{$project['title']}\">\n";
            $projects .= "</div>\n";
            $projects .= "<div class=\"project-content\">\n";
            $projects .= "<h3>{$project['title']}</h3>\n";
            $projects .= "<p>{$project['description']}</p>\n";
            $projects .= "<div class=\"project-tech\">\n";
            
            foreach ($project['technologies'] as $tech) {
                $projects .= "<span class=\"tech-tag\">$tech</span>\n";
            }
            
            $projects .= "</div>\n";
            $projects .= "<div class=\"project-links\">\n";
            
            if ($project['live_url']) {
                $projects .= "<a href=\"{$project['live_url']}\" class=\"btn btn-small\" target=\"_blank\">Live Demo</a>\n";
            }
            
            if ($project['github_url']) {
                $projects .= "<a href=\"{$project['github_url']}\" class=\"btn btn-small btn-outline\" target=\"_blank\">GitHub</a>\n";
            }
            
            $projects .= "</div>\n";
            $projects .= "</div>\n";
            $projects .= "</div>\n";
        }
        
        $projects .= "</div>\n";
        $projects .= "</div>\n";
        $projects .= "</section>\n";
        
        return $projects;
    }
    
    /**
     * Generate contact section
     */
    private function generateContactSection(): string
    {
        $contact = "<section id=\"contact\" class=\"contact\">\n";
        $contact .= "<div class=\"container\">\n";
        $contact .= "<h2>Get In Touch</h2>\n";
        $contact .= "<div class=\"contact-content\">\n";
        $contact .= "<div class=\"contact-info\">\n";
        $contact .= "<div class=\"contact-item\">\n";
        $contact .= "<i class=\"icon-email\"></i>\n";
        $contact .= "<span>{$this->contact['email']}</span>\n";
        $contact .= "</div>\n";
        $contact .= "<div class=\"contact-item\">\n";
        $contact .= "<i class=\"icon-phone\"></i>\n";
        $contact .= "<span>{$this->contact['phone']}</span>\n";
        $contact .= "</div>\n";
        $contact .= "<div class=\"contact-item\">\n";
        $contact .= "<i class=\"icon-location\"></i>\n";
        $contact .= "<span>{$this->contact['location']}</span>\n";
        $contact .= "</div>\n";
        $contact .= "</div>\n";
        $contact .= "<div class=\"contact-form\">\n";
        $contact .= "<form>\n";
        $contact .= "<input type=\"text\" placeholder=\"Name\" required>\n";
        $contact .= "<input type=\"email\" placeholder=\"Email\" required>\n";
        $contact .= "<textarea placeholder=\"Message\" required></textarea>\n";
        $contact .= "<button type=\"submit\" class=\"btn btn-primary\">Send Message</button>\n";
        $contact .= "</form>\n";
        $contact .= "</div>\n";
        $contact .= "</div>\n";
        $contact .= "</div>\n";
        $contact .= "</section>\n";
        
        return $contact;
    }
    
    /**
     * Generate footer
     */
    private function generateFooter(): string
    {
        $footer = "<footer class=\"footer\">\n";
        $footer .= "<div class=\"container\">\n";
        $footer .= "<div class=\"social-links\">\n";
        
        if ($this->social['github']) {
            $footer .= "<a href=\"{$this->social['github']}\" target=\"_blank\">GitHub</a>\n";
        }
        if ($this->social['linkedin']) {
            $footer .= "<a href=\"{$this->social['linkedin']}\" target=\"_blank\">LinkedIn</a>\n";
        }
        if ($this->social['twitter']) {
            $footer .= "<a href=\"{$this->social['twitter']}\" target=\"_blank\">Twitter</a>\n";
        }
        
        $footer .= "</div>\n";
        $footer .= "<p>&copy; " . date('Y') . " PHP Developer. All rights reserved.</p>\n";
        $footer .= "</div>\n";
        $footer .= "</footer>\n";
        
        return $footer;
    }
    
    /**
     * Get portfolio JavaScript
     */
    private function getPortfolioJS(): string
    {
        return "<script>\n" . file_get_contents(__DIR__ . '/portfolio-script.js') . "\n</script>\n";
    }
}

// Personal Branding Coach
class PersonalBrandingCoach
{
    private array $brandingElements = [];
    private array $onlinePresence = [];
    private array $brandingStrategy = [];
    
    public function __construct()
    {
        $this->initializeBrandingElements();
    }
    
    /**
     * Initialize branding elements
     */
    private function initializeBrandingElements(): void
    {
        $this->brandingElements = [
            'unique_value_proposition' => '',
            'target_audience' => '',
            'brand_voice' => '',
            'visual_identity' => '',
            'key_messages' => [],
            'differentiators' => [],
            'expertise_areas' => []
        ];
        
        $this->onlinePresence = [
            'linkedin' => ['profile_url' => '', 'optimization_score' => 0],
            'github' => ['profile_url' => '', 'optimization_score' => 0],
            'personal_website' => ['url' => '', 'optimization_score' => 0],
            'twitter' => ['handle' => '', 'optimization_score' => 0],
            'stackoverflow' => ['profile_url' => '', 'optimization_score' => 0]
        ];
    }
    
    /**
     * Generate branding strategy
     */
    public function generateBrandingStrategy(array $profile): array
    {
        $strategy = [
            'niche_identification' => $this->identifyNiche($profile),
            'brand_positioning' => $this->positionBrand($profile),
            'content_strategy' => $this->developContentStrategy($profile),
            'networking_approach' => $this->developNetworkingApproach($profile),
            'consistency_guidelines' => $this->createConsistencyGuidelines($profile)
        ];
        
        return $strategy;
    }
    
    /**
     * Identify niche
     */
    private function identifyNiche(array $profile): array
    {
        $niches = [
            'full_stack_php_developer' => [
                'description' => 'Full-stack PHP developer specializing in modern frameworks',
                'target_market' => 'Startups and mid-size companies',
                'competition_level' => 'Medium',
                'growth_potential' => 'High'
            ],
            'php_architect' => [
                'description' => 'PHP system architect focusing on scalable solutions',
                'target_market' => 'Enterprise companies',
                'competition_level' => 'Low',
                'growth_potential' => 'Very High'
            ],
            'php_consultant' => [
                'description' => 'PHP development consultant and trainer',
                'target_market' => 'Companies needing expertise',
                'competition_level' => 'Medium',
                'growth_potential' => 'High'
            ],
            'ecommerce_specialist' => [
                'description' => 'PHP developer specializing in e-commerce platforms',
                'target_market' => 'Retail and e-commerce companies',
                'competition_level' => 'Medium',
                'growth_potential' => 'High'
            ]
        ];
        
        return $niches;
    }
    
    /**
     * Position brand
     */
    private function positionBrand(array $profile): array
    {
        return [
            'brand_statement' => 'Experienced PHP developer building scalable, secure web applications',
            'key_differentiators' => [
                'Expert in modern PHP frameworks',
                'Strong problem-solving abilities',
                'Commitment to clean code',
                'Continuous learning mindset'
            ],
            'value_proposition' => 'Delivering high-quality PHP solutions that drive business growth',
            'brand_personality' => 'Professional, innovative, reliable, collaborative'
        ];
    }
    
    /**
     * Develop content strategy
     */
    private function developContentStrategy(array $profile): array
    {
        return [
            'content_pillars' => [
                'Technical tutorials and best practices',
                'Project case studies and insights',
                'Industry trends and analysis',
                'Career development advice'
            ],
            'content_formats' => [
                'Blog posts',
                'Video tutorials',
                'Code repositories',
                'Speaking engagements',
                'Podcast appearances'
            ],
            'posting_frequency' => [
                'blog' => 'Weekly',
                'social_media' => 'Daily',
                'github' => 'As needed',
                'linkedin' => '3-4 times per week'
            ],
            'content_calendar' => $this->generateContentCalendar()
        ];
    }
    
    /**
     * Generate content calendar
     */
    private function generateContentCalendar(): array
    {
        return [
            'monday' => 'Technical tip or best practice',
            'tuesday' => 'Project update or case study',
            'wednesday' => 'Industry news or trend analysis',
            'thursday' => 'Code review or optimization',
            'friday' => 'Career advice or learning resources',
            'weekend' => 'Personal projects or open source contributions'
        ];
    }
    
    /**
     * Develop networking approach
     */
    private function developNetworkingApproach(array $profile): array
    {
        return [
            'online_networking' => [
                'linkedin' => 'Connect with 5-10 professionals weekly',
                'github' => 'Contribute to open source projects',
                'stackoverflow' => 'Answer questions in PHP tags',
                'twitter' => 'Engage with PHP community'
            ],
            'offline_networking' => [
                'meetups' => 'Attend local PHP meetups monthly',
                'conferences' => 'Attend 2-3 conferences yearly',
                'workshops' => 'Participate in technical workshops',
                'mentoring' => 'Mentor junior developers'
            ],
            'networking_goals' => [
                'Build meaningful professional relationships',
                'Establish thought leadership',
                'Create collaboration opportunities',
                'Stay updated with industry trends'
            ]
        ];
    }
    
    /**
     * Create consistency guidelines
     */
    private function createConsistencyGuidelines(array $profile): array
    {
        return [
            'visual_consistency' => [
                'color_palette' => ['#2c3e50', '#3498db', '#2ecc71', '#e74c3c'],
                'typography' => ['Open Sans', 'Roboto', 'Lato'],
                'logo_usage' => 'Consistent across all platforms',
                'image_style' => 'Professional and consistent'
            ],
            'voice_consistency' => [
                'tone' => 'Professional yet approachable',
                'language' => 'Clear, concise, technical but accessible',
                'messaging' => 'Consistent key messages',
                'personality' => 'Helpful, knowledgeable, reliable'
            ],
            'content_consistency' => [
                'quality_standards' => 'High-quality, well-researched content',
                'posting_schedule' => 'Consistent and predictable',
                'brand_values' => 'Emphasize quality, innovation, collaboration',
                'expertise_areas' => 'Focus on PHP and web development'
            ]
        ];
    }
    
    /**
     * Generate personal branding report
     */
    public function generateBrandingReport(array $profile): string
    {
        $strategy = $this->generateBrandingStrategy($profile);
        
        $report = "Personal Branding Strategy Report\n";
        $report .= str_repeat("=", 35) . "\n\n";
        
        $report .= "Niche Identification:\n";
        $report .= str_repeat("-", 18) . "\n";
        foreach ($strategy['niche_identification'] as $niche => $details) {
            $report .= "$niche:\n";
            $report .= "  Description: {$details['description']}\n";
            $report .= "  Target Market: {$details['target_market']}\n";
            $report .= "  Competition: {$details['competition_level']}\n";
            $report .= "  Growth Potential: {$details['growth_potential']}\n\n";
        }
        
        $report .= "Brand Positioning:\n";
        $report .= str_repeat("-", 17) . "\n";
        $report .= "Brand Statement: {$strategy['brand_positioning']['brand_statement']}\n\n";
        $report .= "Key Differentiators:\n";
        foreach ($strategy['brand_positioning']['key_differentiators'] as $differentiator) {
            $report .= "  • $differentiator\n";
        }
        $report .= "\n";
        
        $report .= "Content Strategy:\n";
        $report .= str_repeat("-", 16) . "\n";
        $report .= "Content Pillars:\n";
        foreach ($strategy['content_strategy']['content_pillars'] as $pillar) {
            $report .= "  • $pillar\n";
        }
        $report .= "\nPosting Frequency:\n";
        foreach ($strategy['content_strategy']['posting_frequency'] as $platform => $frequency) {
            $report .= "  $platform: $frequency\n";
        }
        $report .= "\n";
        
        $report .= "Networking Approach:\n";
        $report .= str_repeat("-", 20) . "\n";
        $report .= "Online Networking:\n";
        foreach ($strategy['networking_approach']['online_networking'] as $platform => $activity) {
            $report .= "  $platform: $activity\n";
        }
        $report .= "\n";
        
        return $report;
    }
}

// Resume and Portfolio Examples
class ResumePortfolioExamples
{
    private ResumeBuilder $resumeBuilder;
    private PortfolioBuilder $portfolioBuilder;
    private PersonalBrandingCoach $brandingCoach;
    
    public function __construct()
    {
        $this->resumeBuilder = new ResumeBuilder();
        $this->portfolioBuilder = new PortfolioBuilder();
        $this->brandingCoach = new PersonalBrandingCoach();
    }
    
    public function demonstrateResumeBuilding(): void
    {
        echo "Resume Building Examples\n";
        echo str_repeat("-", 25) . "\n";
        
        // Set up sample resume
        $this->resumeBuilder->setPersonalInfo([
            'name' => 'John Doe',
            'title' => 'Senior PHP Developer',
            'email' => 'john.doe@example.com',
            'phone' => '+1 (555) 123-4567',
            'location' => 'San Francisco, CA',
            'linkedin' => 'https://linkedin.com/in/johndoe',
            'github' => 'https://github.com/johndoe',
            'portfolio' => 'https://johndoe.dev'
        ]);
        
        $this->resumeBuilder->addSkill('Programming Languages', ['PHP', 'JavaScript', 'Python', 'SQL']);
        $this->resumeBuilder->addSkill('Frameworks', ['Laravel', 'Symfony', 'Vue.js', 'React']);
        $this->resumeBuilder->addSkill('Databases', ['MySQL', 'PostgreSQL', 'MongoDB', 'Redis']);
        $this->resumeBuilder->addSkill('Tools', ['Git', 'Docker', 'AWS', 'Jenkins']);
        
        $this->resumeBuilder->addExperience([
            'company' => 'Tech Solutions Inc.',
            'position' => 'Senior PHP Developer',
            'location' => 'San Francisco, CA',
            'start_date' => '2020-01',
            'current' => true,
            'description' => 'Lead development of scalable web applications using PHP and modern frameworks',
            'achievements' => [
                'Improved application performance by 40%',
                'Led team of 5 developers',
                'Implemented CI/CD pipeline reducing deployment time by 60%'
            ],
            'technologies' => ['PHP', 'Laravel', 'MySQL', 'AWS', 'Docker']
        ]);
        
        $this->resumeBuilder->addEducation([
            'institution' => 'University of California, Berkeley',
            'degree' => 'Bachelor of Science',
            'field' => 'Computer Science',
            'start_date' => '2015-09',
            'end_date' => '2019-05',
            'gpa' => '3.8'
        ]);
        
        // Validate resume
        $validation = $this->resumeBuilder->validateResume();
        echo "Resume Validation:\n";
        echo "Valid: " . ($validation['valid'] ? 'Yes' : 'No') . "\n";
        echo "Completeness Score: {$validation['completeness_score']}%\n";
        
        if (!empty($validation['errors'])) {
            echo "Errors:\n";
            foreach ($validation['errors'] as $error) {
                echo "  - $error\n";
            }
        }
        
        if (!empty($validation['warnings'])) {
            echo "Warnings:\n";
            foreach ($validation['warnings'] as $warning) {
                echo "  - $warning\n";
            }
        }
        
        // Generate different formats
        echo "\nGenerated Resume Formats:\n";
        echo "1. Text Format:\n";
        echo substr($this->resumeBuilder->generateResume('text'), 0, 300) . "...\n\n";
        
        echo "2. HTML Format (first 200 chars):\n";
        echo substr($this->resumeBuilder->generateResume('html'), 0, 200) . "...\n\n";
        
        echo "3. Markdown Format (first 200 chars):\n";
        echo substr($this->resumeBuilder->generateResume('markdown'), 0, 200) . "...\n\n";
    }
    
    public function demonstratePortfolioBuilding(): void
    {
        echo "\nPortfolio Building Examples\n";
        echo str_repeat("-", 28) . "\n";
        
        // Set up sample portfolio
        $this->portfolioBuilder->setAbout([
            'content' => 'I am a passionate PHP developer with 5+ years of experience building scalable web applications. I specialize in modern PHP frameworks and love solving complex problems.',
            'skills_highlight' => ['PHP', 'Laravel', 'MySQL', 'JavaScript', 'AWS']
        ]);
        
        $this->portfolioBuilder->setContact([
            'email' => 'john.doe@example.com',
            'phone' => '+1 (555) 123-4567',
            'location' => 'San Francisco, CA'
        ]);
        
        $this->portfolioBuilder->setSocial([
            'github' => 'https://github.com/johndoe',
            'linkedin' => 'https://linkedin.com/in/johndoe',
            'twitter' => 'https://twitter.com/johndoe'
        ]);
        
        $this->portfolioBuilder->addProject([
            'title' => 'E-Commerce Platform',
            'description' => 'Full-featured e-commerce platform with payment processing and inventory management',
            'technologies' => ['PHP', 'Laravel', 'MySQL', 'Stripe API', 'Redis'],
            'features' => ['User authentication', 'Shopping cart', 'Payment processing', 'Admin dashboard'],
            'live_url' => 'https://ecommerce-demo.com',
            'github_url' => 'https://github.com/johndoe/ecommerce',
            'category' => 'Web Application'
        ]);
        
        $this->portfolioBuilder->addProject([
            'title' => 'RESTful API Service',
            'description' => 'Scalable RESTful API with authentication, rate limiting, and documentation',
            'technologies' => ['PHP', 'Symfony', 'PostgreSQL', 'Docker', 'AWS'],
            'features' => ['JWT authentication', 'Rate limiting', 'API documentation', 'Docker deployment'],
            'live_url' => 'https://api-demo.com',
            'github_url' => 'https://github.com/johndoe/api-service',
            'category' => 'API'
        ]);
        
        // Generate portfolio
        $portfolioHTML = $this->portfolioBuilder->generatePortfolio();
        
        echo "Portfolio Generated:\n";
        echo "Sections included:\n";
        echo "  • Navigation menu\n";
        echo "  • Hero section\n";
        echo "  • About section\n";
        echo "  • Skills section\n";
        echo "  • Projects section (" . count($this->portfolioBuilder->projects) . " projects)\n";
        echo "  • Contact section\n";
        echo "  • Footer with social links\n";
        
        echo "\nPortfolio Features:\n";
        echo "  • Responsive design\n";
        echo "  • Modern CSS styling\n";
        echo "  • Interactive JavaScript\n";
        echo "  • SEO optimized\n";
        echo "  • Accessibility compliant\n";
        
        echo "\nPortfolio Statistics:\n";
        echo "  Total projects: " . count($this->portfolioBuilder->projects) . "\n";
        echo "  Social links: " . count(array_filter($this->portfolioBuilder->social)) . "\n";
        echo "  Contact methods: " . count(array_filter($this->portfolioBuilder->contact)) . "\n";
    }
    
    public function demonstratePersonalBranding(): void
    {
        echo "\nPersonal Branding Examples\n";
        echo str_repeat("-", 28) . "\n";
        
        // Sample profile
        $profile = [
            'experience_level' => 'senior',
            'specializations' => ['php', 'laravel', 'api', 'scalability'],
            'interests' => ['open_source', 'mentoring', 'speaking'],
            'goals' => ['thought_leadership', 'consulting', 'team_leading']
        ];
        
        // Generate branding strategy
        $strategy = $this->brandingCoach->generateBrandingStrategy($profile);
        
        echo "Personal Branding Strategy:\n\n";
        
        echo "Niche Options:\n";
        foreach ($strategy['niche_identification'] as $niche => $details) {
            echo "  " . ucwords(str_replace('_', ' ', $niche)) . "\n";
            echo "    Description: {$details['description']}\n";
            echo "    Target: {$details['target_market']}\n";
            echo "    Growth: {$details['growth_potential']}\n\n";
        }
        
        echo "Brand Positioning:\n";
        echo "  Statement: {$strategy['brand_positioning']['brand_statement']}\n";
        echo "  Value Proposition: {$strategy['brand_positioning']['value_proposition']}\n";
        echo "  Personality: {$strategy['brand_positioning']['brand_personality']}\n\n";
        
        echo "Content Strategy:\n";
        echo "  Content Pillars:\n";
        foreach ($strategy['content_strategy']['content_pillars'] as $pillar) {
            echo "    • $pillar\n";
        }
        
        echo "  Posting Schedule:\n";
        foreach ($strategy['content_strategy']['posting_frequency'] as $platform => $frequency) {
            echo "    $platform: $frequency\n";
        }
        
        echo "\nNetworking Approach:\n";
        echo "  Online Activities:\n";
        foreach ($strategy['networking_approach']['online_networking'] as $platform => $activity) {
            echo "    • $platform: $activity\n";
        }
        
        echo "  Offline Activities:\n";
        foreach ($strategy['networking_approach']['offline_networking'] as $activity => $description) {
            echo "    • $description\n";
        }
        
        // Generate comprehensive report
        $report = $this->brandingCoach->generateBrandingReport($profile);
        echo "\n" . substr($report, 0, 500) . "...\n";
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nResume and Portfolio Best Practices\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "Resume Best Practices:\n";
        echo "  • Keep it to 1-2 pages maximum\n";
        echo "  • Use action verbs and quantifiable achievements\n";
        echo "  • Tailor to each job application\n";
        echo "  • Include relevant keywords for ATS\n";
        echo "  • Proofread carefully for errors\n";
        echo "  • Use consistent formatting\n";
        echo "  • Include contact information\n";
        echo "  • Highlight relevant experience\n";
        echo "  • Show career progression\n\n";
        
        echo "Portfolio Best Practices:\n";
        echo "  • Show, don\'t just tell\n";
        echo "  • Include live demos and code repositories\n";
        echo "  • Explain your role in each project\n";
        echo "  • Use professional design\n";
        echo "  • Make it mobile-friendly\n";
        echo "  • Include contact information\n";
        echo "  • Add testimonials if possible\n";
        echo "  • Keep it updated regularly\n";
        echo "  • Optimize for search engines\n\n";
        
        echo "Personal Branding Best Practices:\n";
        echo "  • Be authentic and consistent\n";
        echo "  • Define your unique value proposition\n";
        echo "  • Choose a specific niche\n";
        echo "  • Create valuable content regularly\n";
        echo "  • Network strategically\n";
        echo "  • Maintain professional online presence\n";
        echo "  • Seek speaking opportunities\n";
        echo "  • Mentor others in your field\n";
        echo "  • Stay current with industry trends\n";
        echo "  • Build genuine relationships\n";
    }
    
    public function runAllExamples(): void
    {
        echo "Resume and Portfolio Development Examples\n";
        echo str_repeat("=", 40) . "\n";
        
        $this->demonstrateResumeBuilding();
        $this->demonstratePortfolioBuilding();
        $this->demonstratePersonalBranding();
        $this->demonstrateBestPractices();
    }
}

// Resume and Portfolio Best Practices
function printResumePortfolioBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Resume and Portfolio Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Resume Writing:\n";
    echo "   • Use clear, concise language\n";
    echo "   • Quantify achievements with numbers\n";
    echo "   • Tailor to job requirements\n";
    echo "   • Use action verbs\n";
    echo "   • Keep formatting consistent\n\n";
    
    echo "2. Portfolio Development:\n";
    echo "   • Showcase best work only\n";
    echo "   • Provide context for each project\n";
    echo "   • Include live demos\n";
    echo "   • Use professional design\n";
    echo "   • Make it easy to navigate\n\n";
    
    echo "3. Personal Branding:\n";
    echo "   • Define your unique value\n";
    echo "   • Be authentic and consistent\n";
    echo "   • Choose your target audience\n";
    echo "   • Create valuable content\n";
    echo "   • Network strategically\n\n";
    
    echo "4. Online Presence:\n";
    echo "   • Optimize LinkedIn profile\n";
    echo "   • Maintain active GitHub\n";
    echo "   • Engage on social media\n";
    echo "   • Create personal website\n";
    echo "   • Monitor your online reputation\n\n";
    
    echo "5. Continuous Improvement:\n";
    echo "   • Update portfolio regularly\n";
    echo "   • Seek feedback\n";
    echo "   • Learn new skills\n";
    echo "   • Expand your network\n";
    echo "   • Stay current with trends";
}

// Main execution
function runResumePortfolioDemo(): void
{
    $examples = new ResumePortfolioExamples();
    $examples->runAllExamples();
    printResumePortfolioBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runResumePortfolioDemo();
}
?>
