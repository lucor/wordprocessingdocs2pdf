#!/usr/bin/ruby

@root_dir = Dir.pwd
@root_src = @root_dir + '/source/'
@root_out = @root_dir + '/output/'

def ConvertAPI(file)
  file_name = 'ConvertAPI-' + file + '.pdf'
  src = @root_src + file
  out = @root_out + file_name
  puts "Trying to convert #{file} using Convert API"
  system('curl -F file=@' + src + ' http://do.convertapi.com/Word2Pdf > ' + out)
  puts "./output/#{file_name} created"
end

def GoogleDocs(file)
  file_name = 'GoogleDocs-' + file + '.pdf'
  src = @root_src + file
  out = @root_out + file_name
  Dir.chdir(@root_dir + '/GoogleDocs')
  puts "Trying to convert #{file} using GoogleDocs"
  system('php convert.php ' + src + ' > ' + out)
  puts "./output/#{file_name} created"
end

Dir.chdir(@root_src)
source_files = Dir.glob("*")

source_files.each do |file|
  ConvertAPI(file)
  GoogleDocs(file)
end
