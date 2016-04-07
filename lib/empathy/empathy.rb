module Empathy
  
  VERSION = '0.2.0'

  class Empathy


    def initialize
      @gem_root = File.join(File.dirname(__FILE__),"../../")
    end

    def controller(name, actions)
      filename = name.capitalize + "Controller.php"
      actions.push 'id' if actions.include? 'id'
      actions.push 'created_at' if actions.include? 'created_at'
      actions.push 'modified_at' if actions.include? 'modified_at'
      template = File.read "#{@gem_root}/lib/empathy/controller.php"
      template.sub! "%NAME%", name.capitalize
      Dir.mkdir "views/#{name}" if !Dir.exist? "views/#{name}"
      a = ""
      actions.each do |action|
        a += "    function #{action}() {\n\n    }\n\n"
        File.write "views/#{name}/#{action}.html", "#{name.capitalize}\##{action}"
      end
      template.sub! "%ACTIONS%", a
      File.write("controllers/#{filename}", template) if !File.exist? "controllers/#{filename}"
      return true

    end

    def model(name, fields)
      filename = "#{name.capitalize}.php"
      if File.exist? "models/#{filename}" then
        puts "Model already exists. Exiting"
        return false
      end
      references = fields.select{|f| f.include? 'references'}
      
      fields.unshift 'modified_at:datetime' if !fields.include? 'modified_at:datetime'
      fields.unshift 'created_at:datetime' if !fields.include? 'created_at:datetime'
      fields.unshift 'id:int:key' if !fields.include? 'id:int:key'
      
      template = File.read "#{@gem_root}/lib/empathy/model.php"
      template.gsub! "%NAME%", name.capitalize
      template.gsub! "%TABLE%", "\"#{name}\""
            
      attributes = fields.map{|f| f.split(':')[0]}
      template.gsub! "%FIELDS%", attributes.map{|f| "'#{f}'"}.join(', ')
      template.gsub! "%THIS%", attributes.map{|f| 
        "      $this->#{f} = $data['#{f}'];\n"
      }.join('')
      File.write("models/#{filename}", template)
      
      sql = "CREATE TABLE IF NOT EXISTS #{name} (\n"
      fields.each do |f|
        f = f.split ':'
        if f.length >= 3
          sql += "\t#{f[0]} int PRIMARY KEY NOT NULL AUTO_INCREMENT" if f[2] == 'key'
          sql += "\t#{f[0]} int NOT NULL,\n\tFOREIGN KEY (#{f[0]}) REFERENCES #{f[2]}(id) ON DELETE CASCADE" if f[1] == 'references'
          sql += "\t#{f[0]} #{f[1]} DEFAULT(#{f[3]}" if f[2] == 'default'
        else
          sql += "\t#{f[0]} #{f[1]}"
        end
        sql += ",\n"
      end
      sql = sql[0..-3]+"\n);"
      timestamp = Time.now.strftime "%s"
      File.write "migrations/#{timestamp}_create_#{name}.sql", sql
      return true
    end

    def remove(what, name)
      case what
      when 'model'
        model = name.capitalize
        migration = "DROP TABLE IF EXISTS #{name};"
        if File.exist? "models/#{model}.php"
          File.delete "models/#{model}.php"
          puts "Deleted model #{name}"
        end
        timestamp = Time.now.strftime "%s"
        File.write "migrations/#{timestamp}_delete_#{name}.sql", migration
      when 'controller'
        controller = name.capitalize
        if File.exist? "controllers/#{controller}Controller.php"
          File.delete "controllers/#{controller}Controller.php"
          puts "Deleted controller #{controller}"
        end
        if Dir.exist? "views/#{name}"
          FileUtils.rm_r "views/#{name}"
          puts "Deleted views for #{name} as well"
        end
      end
    end

  end
end
