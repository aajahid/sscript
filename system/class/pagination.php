<?php

/**
* Abdullah Al Jahid
* www.SHAKTI-world.com
* contact@shakti-world.com
*
* I'm 15 Years old PHP developer working professionaly. 
*  
* Pagination Class written By Abdullah AL Jahid (SHAKTI)
* Please visit www.SHAKTI-world.com to know about me and my works.
* Its Free to use in your project, but this message must be here.
* Please contact me before using this class in your project.
* 
* 
*/


  class pagination 
  {
      var $page;
      var $pagetotal;
      var $sql;
      var $query;
      var $limit = 10;
      var $pagereferer;
      var $totalResult;
      var $limitStr;
      var $ajax_type = false;
      var $ajax_url;
      var $div_pagination_id;
      
      function pagination ( $thesql = false )
        {
            if ($thesql) $this->sql = $thesql;
        }

      function execute( $totalItem )
      {
          $this->totalResult = $totalItem;
          $this->pagetotal = ceil( ($this->totalResult) / $this->limit );

          if( (int)isset($_GET['page']) )
          {
              $this->page = $_GET['page'];
          }
          else
          {
              $this->page = 1;
          }

          if ( !$this->pagetotal )
          {
              $this->page = 1;
          }
          else if ( $this->page > $this->pagetotal )
          {
              $this->page = $this->pagetotal;
          }
          $this->limitStr = " LIMIT ".(($this->page-1)*$this->limit).", $this->limit";
      }

      function getLimitStr(){
          return $this->limitStr;
      }
        
      function getQuery()
        {
            $this->totalResult = mysql_num_rows(mysql_query($this->sql));
            $this->pagetotal = ceil(($this->totalResult)/$this->limit);

            if ((int)isset($_GET['page']))
            {
                $this->page = $_GET['page'];
            }
            else
            {
                $this->page = 1;
            }
            if ( !$this->pagetotal )
            {
                $this->page = 1;
            }
            else if ( $this->page > $this->pagetotal )
            {
                $this->page = $this->pagetotal;
            }

            $this->sql .= " LIMIT ".(($this->page-1)*$this->limit).", $this->limit";
            $this->query = mysql_query($this->sql);
            return $this->query;
        }
        
      function isPaginationRquired()
        {
            if ( $this->totalResult > $this->limit )
                {
                    return true;
                }
            else
                {
                    return false;
                }
        }
        
      function showPagination()
        {
            if ( $this->isPaginationRquired() )
            {
            $html = '
                <table width="100%">
                    <tr>
                    <td width="50%">Showing Page '.$this->page.' of '.$this->pagetotal.'</td>
                        <td align="right">';
                        if ($this->pagereferer)
                        {
                            $this->pagereferer .= '&';
                        }
                        if ( isset($_GET['id']) )$this->pagereferer .= "id=".$_GET['id']."&";

                        if ($this->pagetotal<=5)
                        {
                            $from = 1;
                            $to = $this->pagetotal;
                        }
                        else if ($this->page == 1 || $this->page == 2 || $this->page == 3)
                        {
                            $from = 1;
                            $to = 5;
                        }
                        else if ($this->page == $this->pagetotal-1)
                        {
                            $from = $this->page-3;
                            $to = $this->page+1;
                        }
                        else if ($this->page == $this->pagetotal)
                        {
                            $from = $this->page-4;
                            $to = $this->pagetotal;
                        }
                        else
                        {
                            $from = $this->page-2;
                            $to = $this->page+2;
                        }
             $html .= '<div class="pagination"><ul>';
                        if ($this->page>1)
                        {
                            $html .= '<li><a href="?'.$this->pagereferer.'page=1" title="Go to first page"';
                            if($this->ajax_type)
                            {
                                $html .= ' onclick="ajaxPagination(\''.$this->ajax_url.'?'.$this->pagereferer.'page=1\',\''.$this->div_pagination_id.'\'); return false;"';
                            }
                            $html .= '>&laquo;&laquo;</a></li>
                            <li><a href="?'.$this->pagereferer.'page='.($this->page-1).'" title="Go to previous page"';
                            if($this->ajax_type)
                            {
                                $html .= ' onclick="ajaxPagination(\''.$this->ajax_url.'?'.$this->pagereferer.'page='.($this->page-1).'\',\''.$this->div_pagination_id.'\'); return false;"';
                            }
                            $html .= '>&laquo;</a></li>';
                        }
                        else
                        {
                            $html .= '<li class="disabled"><a>&laquo;&laquo;</a></li>
                            <li class="disabled"><a>&laquo;</a></li>';
                        }
                        for ($i=$from; $i<=$to; $i++)
                        {
                            if ($this->page == $i)
                            {
                                $html .= '<li class="active"><a href="#">'.$i.'</a></li>';
                            }
                            else
                            {
                                $html .= '<li><a href="?'.$this->pagereferer.'page='.$i.'" title="Go to page '.$i.'"';
                                if($this->ajax_type)
                                {
                                    $html .= ' onclick="ajaxPagination(\''.$this->ajax_url.'?'.$this->pagereferer.'page='.$i.'\',\''.$this->div_pagination_id.'\'); return false;"';
                                }
                                $html .= '>'.$i.'</a></li>';
                            }
                        }
                        if ($this->page<$this->pagetotal)
                        {
                            $html .= '<li><a href="?'.$this->pagereferer.'page='.($this->page+1).'" title="Go to next page"';
                            if($this->ajax_type)
                            {
                                $html .= ' onclick="ajaxPagination(\''.$this->ajax_url.'?'.$this->pagereferer.'page='.($this->page+1).'\',\''.$this->div_pagination_id.'\'); return false;"';
                            }
                            $html .= '>&raquo;</a></li>
                            <li><a href="?'.$this->pagereferer.'page='.$this->pagetotal.'" title="Go to last page">&raquo;&raquo;</a></li>';
                        }
                        else
                        {
                            $html .= '<li class="disabled"><a>&raquo;</a></li>
                            <li class="disabled"><a>&raquo;&raquo;</a></li>';
                        }
                        $html .= '</ul>
                            </div>
                        </td>
                    </tr>
                </table>';
            return $html;
            }
            
            
            
        }

      function setTypeAjax($ajax_url,$div_pagination_id)
      {
          $this->ajax_type = true;
          $this->ajax_url = $ajax_url;
          $this->div_pagination_id = $div_pagination_id;
      }
      
  }
?>