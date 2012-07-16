def Abiword(file)
  file_name = 'Abiword-' + file + '.pdf'
  src = @root_src + file
  out = @root_out + file_name
  puts "Trying to convert #{file} using Abiword"
  command = 'abiword --to=' + out + ' ' + src
  
  time = Benchmark.realtime do
    system(command)
  end
  
  if ($?.exitstatus == 0)
    puts "./output/#{file_name} created in #{time*1000} milliseconds"
  else
    puts "An error has been occurred during conversion."
  end
end

def ConvertAPI(file)
  file_name = 'ConvertAPI-' + file + '.pdf'
  src = @root_src + file
  out = @root_out + file_name
  puts "Trying to convert #{file} using Convert API"
  
  time = Benchmark.realtime do
    system('curl -s -F file=@' + src + ' http://do.convertapi.com/Word2Pdf > ' + out)
  end
  
  if ($?.exitstatus == 0)
    puts "./output/#{file_name} created in #{time*1000} milliseconds"
  else
    puts "An error has been occurred during conversion."
  end
end

def doxument(file, config)
  file_name = 'doxument-' + file + '.pdf'
  src = @root_src + file
  out = @root_out + file_name
  Dir.chdir(@root_dir + '/doxument')
  puts "Trying to convert #{file} using doxument"
  
  time = Benchmark.realtime do
    system('php convert.php ' + [config['apiKey'], config['apiToken'], src, out].reject(&:empty?).join(' '))
  end
  
  if ($?.exitstatus == 0)
    puts "./output/#{file_name} created in #{time*1000} milliseconds"
  else
    puts "An error has been occurred during conversion."
  end
end

def GoogleDocs(file, config)
  file_name = 'GoogleDocs-' + file + '.pdf'
  src = @root_src + file
  out = @root_out + file_name
  Dir.chdir(@root_dir + '/GoogleDocs')
  puts "Trying to convert #{file} using GoogleDocs"
  
  time = Benchmark.realtime do
    system('php convert.php ' + [config['username'], config['password'], src, out].reject(&:empty?).join(' ') + ' > ' + out)
  end  
  
  if ($?.exitstatus == 0)
    puts "./output/#{file_name} created in #{time*1000} milliseconds"
  else
    puts "An error has been occurred during conversion."
  end
end

def LibreOffice(file)
  require_relative 'os'
  require 'fileutils'
  require 'pathname'
  
  prefix_path = ''
  if OS.mac?
    prefix_path = "/Applications/LibreOffice.app/Contents/MacOS/"
  end
  
  file_name = 'LibreOffice-' + file + '.pdf'
  src = @root_src + file
  out = @root_out + file_name
  temp_file = @root_out + File.basename(file, '.doc') + '.pdf'
  puts "Trying to convert #{file} using LibreOffice"
  
  time = Benchmark.realtime do
    system(prefix_path + 'soffice --invisible --convert-to pdf --outdir ' + @root_out + ' ' + src)
  end
  
  puts "Renaming generated file..."
  FileUtils.mv(temp_file, out)
  if ($?.exitstatus == 0)
    puts "./output/#{file_name} created in #{time*1000} milliseconds"
  else
    puts "An error has been occurred during conversion."
  end
end

def saaspose(file, config)
  require 'saasposesdk'
  
  file_name = 'saaspose-' + file + '.pdf'
  src = @root_src + file
  out = @root_out + file_name

  saveFormat = 'pdf'
  
  puts "Trying to convert #{file} using saaspose"
  
  time = Benchmark.realtime do
    productURI = 'http://api.saaspose.com/v1.0'
    Common::Product.setBaseProductUri(productURI)
    
    appSID = config['appSID']
    appKey = config['appKey']
    
    Common::SaasposeApp.new(appSID,appKey)
    
    # Upload
    Storage::Folder.uploadFile(src,'')
    puts 'file uploaded successfully'
    
    urlDoc = $productURI + '/words/' + file + '?format=' + saveFormat
    signedURL = Common::Utils.sign(urlDoc)
    response = RestClient.get(signedURL, :accept => 'application/json')
    Common::Utils.saveFile(response, out)
  end
  
  puts "./output/#{file_name} created in #{time*1000} milliseconds"
end
