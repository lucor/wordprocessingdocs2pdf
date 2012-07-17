#!/usr/bin/ruby

@root_dir = Dir.pwd
@root_src = @root_dir + '/source/'
@root_out = @root_dir + '/output/'

require 'yaml'
require 'benchmark'

config = YAML.load(File.open("./config.yml"))

Dir.glob(File.dirname(__FILE__) + '/libs/*.rb') {|file| require_relative file}

Dir.mkdir(@root_out) unless File.exists?(@root_out)

Dir.chdir(@root_src)
source_files = Dir.glob("*.doc*")

source_files.each do |file|
  Abiword(file)
  ConvertAPI(file)
  doxument(file, config['doxument'])
  GoogleDocs(file, config['googledocs'])
  LibreOffice(file)
  saaspose(file, config['saaspose'])
end