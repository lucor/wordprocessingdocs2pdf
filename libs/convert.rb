def Abiword(file)
  file_name = 'Abiword-' + file + '.pdf'
  src = @root_src + file
  out = @root_out + file_name
  puts "Trying to convert #{file} using Abiword"
  command = 'abiword --to=' + out + ' ' + src
  system(command)
  if ($?.exitstatus == 0)
    puts "./output/#{file_name} created"
  else
    puts "An error has been occurred during conversion."
  end
end

def ConvertAPI(file)
  file_name = 'ConvertAPI-' + file + '.pdf'
  src = @root_src + file
  out = @root_out + file_name
  puts "Trying to convert #{file} using Convert API"
  system('curl -F file=@' + src + ' http://do.convertapi.com/Word2Pdf > ' + out)
  if ($?.exitstatus == 0)
    puts "./output/#{file_name} created"
  else
    puts "An error has been occurred during conversion."
  end
end

def GoogleDocs(file)
  file_name = 'GoogleDocs-' + file + '.pdf'
  src = @root_src + file
  out = @root_out + file_name
  Dir.chdir(@root_dir + '/GoogleDocs')
  puts "Trying to convert #{file} using GoogleDocs"
  system('php convert.php ' + src + ' > ' + out)
  if ($?.exitstatus == 0)
    puts "./output/#{file_name} created"
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
  system(prefix_path + 'soffice --invisible --convert-to pdf --outdir ' + @root_out + ' ' + src)
  puts "Renaming generated file..."
  FileUtils.mv(temp_file, out)
  if ($?.exitstatus == 0)
    puts "./output/#{file_name} created"
  else
    puts "An error has been occurred during conversion."
  end
end
