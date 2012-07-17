def doxument(file, config)
  file_name = 'doxument-' + file + '.pdf'
  src = @root_src + file
  out = @root_out + file_name
  Dir.chdir(@root_dir + '/scripts')
  puts "Trying to convert #{file} using doxument"
  
  time = Benchmark.realtime do
    system('php doxument.php ' + [config['apiKey'], config['apiToken'], src, out].reject(&:empty?).join(' '))
  end
  
  if ($?.exitstatus == 0)
    puts "./output/#{file_name} created in #{time*1000} milliseconds"
  else
    puts "An error has been occurred during conversion."
  end
end
