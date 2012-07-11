

# # # # # # # # #
# /35/os.rb
#
# by               Jan Lelis
# e-mail:          mail@janlelis.de
# type/version:    ruby 
# snippet url:     http://rbJL.net/35/os.rb
# original post:   http://rbJL.net/35-how-to-properly-check-for-your-ruby-interpreter-version-and-os
# license:         CC-BY (DE)
#
# (c) 2010 Jan Lelis.

require 'rbconfig'

module OS
  class << self
    def is?(what)
      what === RbConfig::CONFIG['host_os']
    end
    alias is is?

    def to_s
      RbConfig::CONFIG['host_os']
    end
  end

  module_function

  def linux?
    OS.is? /linux|cygwin/
  end

  def mac?
    OS.is? /mac|darwin/
  end

  def bsd?
    OS.is? /bsd/
  end

  def windows?
    OS.is? /mswin|win|mingw/
  end

  def solaris?
    OS.is? /solaris|sunos/
  end

  def posix?
    linux? or mac? or bsd? or solaris? or Process.respond_to?(:fork)
  end

end
