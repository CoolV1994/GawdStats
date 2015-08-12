package com.gawdscape.statsync;

import org.bukkit.entity.Player;
import org.bukkit.event.EventHandler;
import org.bukkit.event.Listener;
import org.bukkit.event.player.PlayerQuitEvent;

/**
 * Created by Vinnie on 12/30/2014.
 */
public class PlayerListener implements Listener {
    private final StatSync plugin;

    public PlayerListener(StatSync instance) {
        plugin = instance;
    }

    @EventHandler
    public void onPlayerQuit(PlayerQuitEvent event) {
        Player p = event.getPlayer();
        if (plugin.shouldSync(p.getName())) {
            String response = plugin.syncStats(p.getUniqueId(), p.getName());
            plugin.getLogger().info(response);
        }
    }
}
