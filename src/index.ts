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

const arrowDown = require('./assets/arrow-down.svg') as string;
const arrowUp = require('./assets/arrow-up.svg') as string;

const init = () => {
    let toshow = parseInt(document.getElementById("studentstracker-list").dataset.show);
    let block_li = document.getElementById("studentstracker-list").getElementsByTagName("li");
    for (let i = 0, j = block_li.length; i < j; i++) {
        if (i > toshow - 1 && toshow !== 0) {
            block_li[i].style.display = "none";
        }
    }

    if (toshow > 0 && toshow < block_li.length) {
        let showmore = document.getElementById("tracker_showmore");
        let btn = document.createElement("button");
        btn.innerHTML = arrowDown;
        showmore.appendChild(btn);

        let showless = document.getElementById("tracker_showless");
        let btnless = document.createElement("button");
        btnless.innerHTML = arrowUp;
        showless.appendChild(btnless);
        showless.style.display = "none";

        showmore.addEventListener("click", function() {
            showMore();
            showmore.style.display = "none";
            showless.style.display = "block";
        });

        showless.addEventListener("click", function() {
            showLess(toshow);
            showmore.style.display = "block";
            showless.style.display = "none";
        });
    }
};

const showMore = () => {
    let block_li = document.getElementById("studentstracker-list").getElementsByTagName("li");
    for (let i = 0, j = block_li.length; i < j; i++) {
        block_li[i].style.display = "block";
    }
};

const showLess = (toshow: number) => {
    let block_li = document.getElementById("studentstracker-list").getElementsByTagName("li");
    for (let i = 0, j = block_li.length; i < j; i++) {
        if (i > toshow - 1 && toshow !== 0) {
            block_li[i].style.display = "none";
        }
    }
};

export { init };
