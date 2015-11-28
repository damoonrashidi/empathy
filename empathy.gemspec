
# -*- encoding: utf-8 -*-
$:.push('lib')
require "empathy/empathy"

Gem::Specification.new do |s|
  s.name     = "empathy"
  s.version  = Empathy::VERSION.dup
  s.date     = "2015-11-22"
  s.summary  = "Empathy generates controller, models and view for your project"
  s.email    = "damoon.rashidi@gmail.com"
  s.homepage = "http://sfzombie.com"
  s.authors  = ['Damoon Rashidi']
  s.license  = "MIT"
  
  s.description = "Create "
  
  dependencies = [
    # Examples:
    # [:runtime,     "rack",  "~> 1.1"],
    # [:development, "rspec", "~> 2.1"],
  ]
  
  s.files         = Dir['bin/*', 'lib/**/*', 'templates/**/*']
  s.test_files    = Dir['test/**/*'] + Dir['spec/**/*']
  s.executables   = Dir['bin/*'].map { |f| File.basename(f) }
  s.require_paths = ["lib"]
  
  ## Make sure you can build the gem on older versions of RubyGems too:
  s.rubygems_version = "2.4.8"
  s.required_rubygems_version = Gem::Requirement.new(">= 0") if s.respond_to? :required_rubygems_version=
  s.specification_version = 3 if s.respond_to? :specification_version
  
  dependencies.each do |type, name, version|
    if s.respond_to?("add_#{type}_dependency")
      s.send("add_#{type}_dependency", name, version)
    else
      s.add_dependency(name, version)
    end
  end
end
