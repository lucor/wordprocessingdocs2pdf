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
