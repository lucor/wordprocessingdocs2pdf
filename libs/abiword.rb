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