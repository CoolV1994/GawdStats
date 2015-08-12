package com.gawdscape.statsync;

import org.bukkit.Bukkit;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;

import java.util.Iterator;

/**
 * Created by Vinnie on 7/14/2015.
 */
public class SyncCommand implements CommandExecutor {
	private final StatSync plugin;

	public SyncCommand(StatSync instance) {
		plugin = instance;
	}

	@Override
	public boolean onCommand(CommandSender sender, Command cmd, String label, String[] args) {
		if (!(sender instanceof Player)) {
			if (args.length == 0) {
				sender.sendMessage("[StatSync] Enter username to sync.");
				return false;
			}
			if (args[0].equals("all")) {
				Iterator players = Bukkit.getOnlinePlayers().iterator();
				while (players.hasNext()) {
					Player p = (Player) players.next();
					String response = plugin.syncStats(p.getUniqueId(), p.getName());
					sender.sendMessage("[Stat Sync] " + response);
				}
				return true;
			}
			Player p = Bukkit.getPlayer(args[0]);
			String response = plugin.syncStats(p.getUniqueId(), p.getName());
			sender.sendMessage("[Stat Sync] " + response);
			return true;
		}

		if (args.length == 0) {
			if (sender.hasPermission("statsync.sync")) {
				String response = plugin.syncStats(((Player) sender).getUniqueId(), sender.getName());
				if (response != null) {
					sender.sendMessage("[Stat Sync] " + response);
				}
			}
			return true;
		}
		if (args[0].equals("all")) {
			if (!sender.hasPermission("statsync.all")) {
				sender.sendMessage("[Stat Sync] No permission.");
				return true;
			}
			Iterator players = Bukkit.getOnlinePlayers().iterator();
			while (players.hasNext()) {
				Player p = (Player) players.next();
				String response = plugin.syncStats(p.getUniqueId(), p.getName());
				sender.sendMessage("[Stat Sync] " + response);
			}
			return true;
		}
		if (!sender.hasPermission("statsync.other")) {
			sender.sendMessage("[Stat Sync] No permission.");
			return true;
		}
		Player p = Bukkit.getPlayer(args[0]);
		String response = plugin.syncStats(p.getUniqueId(), p.getName());
		if (response != null) {
			sender.sendMessage(response);
		}
		return true;
	}
}
