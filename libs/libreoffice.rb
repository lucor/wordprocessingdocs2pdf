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