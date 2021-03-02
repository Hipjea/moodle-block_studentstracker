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
 * @package    block_studentstracker
 * @author     Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define([], function() {
    return {
        init: function() {
            let self = this;
            let toshow = parseInt(document.getElementById("studentstracker-list").dataset.show);
            let block_li = document.getElementById("studentstracker-list").getElementsByTagName("li");
            for (let i = 0, j = block_li.length; i < j; i++) {
                if (i > parseInt(toshow - 1) && toshow !== 0) {
                    block_li[i].style.display = "none";
                }
            }

            if (toshow > 0 && toshow < block_li.length) {
                let showmore = document.getElementById("tracker_showmore");
                let btn = document.createElement("button");
                let svgarrowd = '<svg width="18" height="18" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">';
                svgarrowd = svgarrowd + '<path fill="#FFFFFF" d="M1683 808l-742 741q-19 19-45 19t-45-19l-742-741q-19-19-19';
                svgarrowd = svgarrowd + '-45.5t19-45.5l166-165q19-19 45-19t45 19l531 531 531-531q19-19 45-19t45 19l166 165q19';
                svgarrowd = svgarrowd + ' 19 19 45.5t-19 45.5z"/></svg>';
                btn.innerHTML = svgarrowd;
                showmore.appendChild(btn);
                let showless = document.getElementById("tracker_showless");
                let btnless = document.createElement("button");

                let svgarrowu = '<svg width="18" height="18" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">';
                svgarrowu = svgarrowu + '<path fill="#FFFFFF" d="M1683 1331l-166 165q-19 19-45 19t-45-19l-531-531-531 ';
                svgarrowu = svgarrowu + '531q-19 19-45 19t-45-19l-166-165q-19-19-19-45.5t19-45.5l742-741q19-19 45-19t45 ';
                svgarrowu = svgarrowu + '19l742 741q19 19 19 45.5t-19 45.5z"/></svg>';
                btnless.innerHTML = svgarrowu;
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
            let block_li = document.getElementById("studentstracker-list").getElementsByTagName("li");
            for(let i = 0, j = block_li.length; i < j; i++) {
                  block_li[i].style.display = "block";
            }
        },

        showLess: function(toshow) {
            let block_li = document.getElementById("studentstracker-list").getElementsByTagName("li");
            for(let i = 0, j = block_li.length; i < j; i++) {
                if (i > parseInt(toshow - 1) && toshow !== 0) {
                    block_li[i].style.display = "none";
                }
            }
        }
    };
});
