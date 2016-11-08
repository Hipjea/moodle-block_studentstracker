// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin capabilities
 *
 * @package    blocks_studentstracker
 * @author     Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define([], function() {
  return {
    init: function() {
    	var self = this;
    	var toshow = parseInt(document.getElementById("studentstracker-list").dataset.show);
      var block_li = document.getElementById("studentstracker-list").getElementsByTagName("li");
      for(var i=0, j=block_li.length; i<j; i++) {
      	if (i>parseInt(toshow-1) && toshow!==0) {
      		block_li[i].style.display = "none";
      	}
      }

      if (toshow > 0) {
	      var showmore = document.getElementById("studentstracker_showmore");
	      var btn = document.createElement("button");
				btn.innerHTML = "&or;";
				showmore.appendChild(btn);
				
				var showless = document.getElementById("studentstracker_showless");
	      var btnless = document.createElement("button");
				btnless.innerHTML = "&and;";
				showless.appendChild(btnless);
				showless.style.display = "none";
				
				showmore.addEventListener("click", function() {
					self.showMore();
					showmore.style.display = "none";
					showless.style.display = "block";
				});

				showless.addEventListener("click", function() {
					self.showLess(toshow);
					showmore.style.display = "block";
					showless.style.display = "none";
				});
			}
    },

    showMore: function() {
    	var block_li = document.getElementById("studentstracker-list").getElementsByTagName("li");
    	for(var i=0, j=block_li.length; i<j; i++) {
      	block_li[i].style.display = "block";
      }
    },

    showLess: function(toshow) {
    	var block_li = document.getElementById("studentstracker-list").getElementsByTagName("li");
    	for(var i=0, j=block_li.length; i<j; i++) {
      	if (i>parseInt(toshow-1) && toshow!==0) {
      		block_li[i].style.display = "none";
      	}
      }
    }
  };
});
