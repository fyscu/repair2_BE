<?php
header('Content-type: image/svg+xml');
$word  = ["未分配", "待确认", "已取消", "已确认", "已完成", "重分配"];
$color = ["#ccc", "#00b7ff", "#ccc", "#399", "#4c1", "#ccc"];
?>

<svg xmlns="http://www.w3.org/2000/svg" width="90" height="20">
    <linearGradient id="a" x2="0" y2="100%">
        <stop offset="0" stop-color="#bbb" stop-opacity=".1"/>
        <stop offset="1" stop-opacity=".1"/>
    </linearGradient>
    <rect rx="3" width="60" height="20" fill="#555"/>
    <rect rx="3" x="37" width="53" height="20" fill="<?php echo($color[$status]); ?>"/>
    <path fill="<?php echo($color[$status]); ?>" d="M37 0h4v20h-4z"/>
    <rect rx="3" width="90" height="20" fill="url(#a)"/>
    <g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="14">
        <text x="18" y="16" fill="#010101" fill-opacity=".3">状态</text>
        <text x="18" y="15">状态</text>
        <text x="63" y="16" fill="#010101" fill-opacity=".3"></text>
        <text x="63" y="15"><?php echo($word[$status]); ?></text>
    </g>
</svg>
