<?php

    class upload
        {
            var $file = array();
            var $basename;
            var $name;
            var $extension;
            var $source;

            
            var $width;
            var $height;
            
            var $src_width;
            var $src_height;
            
            var $dst_x = 0;
            var $dst_y = 0;
            
            var $src_x = 0;
            var $src_y = 0;
            
            var $type;
            
            var $max_width = 640;
            var $max_height = 480;
            var $quality = 80;
            
            var $upload_dir = UPLOAD_DIR;

        public $dest_file_ext = '.jpg';
            
            
            
            function upload ( $file )
                {

                    if ( is_array( $file ) )
                    {
                        $this->file = $file['tmp_name'];
                        $name       = $file['name'];
                    }
                    else
                    {
                        $this->file = $file;
                        $name       = $this->file;
                    }

                    $path_info          = pathinfo( $name );
                    $this->basename     = $path_info['basename'];
                    $this->name         = $path_info['filename'];
                    $this->extension    = strtolower( $path_info['extension'] );
                        
                }
                
                
                
                
            function save_file()
                {
                    return move_uploaded_file($this->file, $this->upload_dir.$this->name.'.'.$this->extension);
                }
                
                
                
            function get_source()
                {
                    list($this->width, $this->height, $this->type) = getimagesize($this->file);
                    switch($this->type)
                        {
                            case 1:
                                $this->source = imagecreatefromgif($this->file);
                                break;
                            
                            case 2:
                                $this->source = imagecreatefromjpeg($this->file);
                                break;
                            
                            case 3:
                                $this->source = imagecreatefrompng($this->file);
                                break;
                            
                            default:
                                @unlink($this->file);
                                $this->source = false;
                                break;
                        } 
                        
                    if ( !$this->source ) 
                        {
                            return false;
                        }  
                    else
                        {
                            return true;
                        }
                    
                }
                
                
            /**
            * get the image width and height to resize. 
            *  
            * @param mixed $type available value 1 = Keep the ratio, 2 = No ratio, 3 = crop the image.
            */
            function getSize( $type = 1 )
                {
                  // Keep the ratio  
                  if( $type == 1 )
                    {  
                        if ($this->max_width < $this->width || $this->max_height < $this->height)
                        {
                            $checkheight = ceil(($this->max_width/$this->width)*$this->height);

                            if ($checkheight<$this->max_height) 
                            {
                                $this->max_height = $checkheight;
                            }
                            else
                            {
                                $this->max_width = ceil($this->width*($this->max_height/$this->height));
                            }
                        }
                        else
                        {
                            $this->max_width = $this->width;
                            $this->max_height = $this->height;
                        }
                        
                        $this->src_width = $this->width;
                        $this->src_height = $this->height;
                    }
                  // No retio, just resize (bad Idea)
                  elseif( $type == 2 )
                    {
                        
                        $this->src_width = $this->max_width;
                        $this->src_height = $this->max_height;
                    }      
                  // Crop the image
                  elseif( $type == 3 )
                    {
                        
                        
                        if( $this->width == $this->height )
                            {
                                $this->src_width = $this->width;
                                $this->src_height = $this->height;
                            }
                        elseif( $this->width > $this->height )
                            {
                                $this->src_width = $this->height;
                                $this->src_height = $this->height;
                            }
                        else
                            {
                                $this->src_width = $this->width;
                                $this->src_height = $this->width;
                            }
                            
                    }
                    
                    
                }
                
                
            function saveImage($type = 1)
            {
                if( !$this->get_source() )
                {
                    return false;
                }
                else
                {
                    $this->getSize($type);
                    $this->makeImage();
                }
                    
            }
                
        function makeImage()
        {
            $this->get_source();
                    $resize = imagecreatetruecolor($this->max_width, $this->max_height);
                    imagecopyresampled($resize, $this->source, $this->dst_x, $this->dst_y, $this->src_x, $this->src_y, $this->max_width, $this->max_height, $this->src_width, $this->src_height);
                    imagejpeg($resize, $this->upload_dir.$this->name.$this->dest_file_ext, $this->quality);
                    imagedestroy($resize);
                }
                
                
            function createThumb($size)
                {
                   if( $this->get_source() )
                        { 
                            $this->max_height = $size;
                            $this->max_width = $size;
                            

                            $this->getSize(3);
                            
                            if( $this->width > $this->height )
                            {
                                $this->src_x = (($this->width - $this->height)/2);
                            }
                            elseif( $this->height > $this->width )
                            {
                                $this->src_y = (($this->height - $this->width)/2);
                            }

                            $this->makeImage();
                            
                        }

                    
                }
                
                
            function clear()
                {
                    @unlink($this->file);    
                }
            
        }
        
        
        
?>
