#!/usr/bin/ruby

@root_dir = Dir.pwd
@root_src = @root_dir + '/source/'
@root_out = @root_dir + '/output/'

def ConvertAPI(file)
  src = @root_src + file
  out = @root_out + file + '.pdf'
  puts "Trying to conver #{file} using Convert API"
  exec('curl -F file=@' + src + ' http://do.convertapi.com/Word2Pdf > ' + out)
end

def GoogleDocs(file)
  src = @root_src + file
  out = @root_out + file + '.pdf'
  
  
  Dir.chdir(@root_dir + '/GoogleDocs')
  puts "Trying to conver #{file} using GoogleDocs"
  exec('php convert.php ' + src + ' > ' + out)
end

Dir.chdir(@root_src)
source_files = Dir.glob("*")

source_files.each do |file|
  #ConvertAPI(file)
  GoogleDocs(file)
end
