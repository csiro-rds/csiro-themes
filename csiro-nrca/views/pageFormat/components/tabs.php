<?php
// TODO Second level navigation as tab dropdowns
/** @var AppNavigation $t_nav */
$t_nav = $this->getVar('nav');
$va_nav_info = $t_nav->getNavInfo(2);
?>
<?php if (is_array($va_nav_info) && sizeof($va_nav_info) > 0): ?>
    <?php
    $va_nav_info = $t_nav->applyDynamicNavigation($va_nav_info);
    $vs_destination = $t_nav->getDestinationAsNavigationPath();
    $vs_base = preg_replace('/^([^\\/]+\\/[^\\/]+)\\/.+$/', '$1', $vs_destination);
    ?>
    <nav>
        <ul class="nav nav-tabs">
            <?php foreach ($va_nav_info as $vs_key => $va_menu_item): ?>
                <?php
                $vb_has_access = $t_nav->evaluateRequirements($va_menu_item['requires']);
                ?>
                <?php if (isset($va_menu_item['hideIfNoAccess']) && !$va_menu_item['hideIfNoAccess'] || $vb_has_access): ?>
                    <?php
                    $va_route = $va_menu_item['default'];
                    $va_parameters = $t_nav->parseAdditionalParameters($va_menu_item['parameters']);
                    $va_classes = array();
                    if ($vs_destination === "$vs_base/$vs_key") {
                        $va_classes[] = 'active';
                    }
                    $vb_disabled = isset($va_menu_item['disabled']) && $va_menu_item['disabled'];
                    if ($vb_disabled) {
                        $va_classes[] = 'disabled';
                    }
                    ?>
                    <li role="presentation" class="<?php print join(' ', $va_classes); ?>">
                        <?php if ($vb_disabled): ?>
                            <a href="#" onclick="return false;"><?php print $va_menu_item['displayName']; ?></a>
                        <?php else: ?>
                            <a href="<?php print caNavUrl($this->request, $va_route['module'], $va_route['controller'], $va_route['action'], $va_parameters); ?>"><?php print $va_menu_item['displayName']; ?></a>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </nav>
<?php endif; ?>
