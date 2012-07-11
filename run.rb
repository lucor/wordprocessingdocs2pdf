#!/usr/bin/ruby

@root_dir = Dir.pwd
@root_src = @root_dir + '/source/'
@root_out = @root_dir + '/output/'

Dir.glob(File.dirname(__FILE__) + '/libs/*') {|file| require_relative file}

Dir.mkdir(@root_out)

Dir.chdir(@root_src)
source_files = Dir.glob("*")

source_files.each do |file|
  ConvertAPI(file)
  GoogleDocs(file)
  LibreOffice(file)
end