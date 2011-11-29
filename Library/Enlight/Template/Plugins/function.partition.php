<?php
function smarty_function_partition($params, $smarty)
{
	$list = $params['array'];
	$p = $params['parts'];
    $listlen = count( $list );
    $partlen = floor( $listlen / $p );
    $partrem = $listlen % $p;
    $partition = array();
    $mark = 0;
    for ($px = 0; $px < $p; $px++) {
        $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
        $partition[$px] = array_slice( $list, $mark, $incr );
        $mark += $incr;
    }
    $smarty->assign($params['assign'],$partition);
}