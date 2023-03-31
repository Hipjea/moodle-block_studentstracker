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

const arrowDown = require('../../assets/arrow-down.svg');
const arrowUp = require('../../assets/arrow-up.svg');

/**
 * Init script.
 *
 * @param {object} root The root element for studentstracker.
 * @return {void}
 */
export const init = root => {
  let toshow = parseInt(root.dataset.show);
  let block_li = root.getElementsByTagName("li");

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

    showmore.addEventListener("click", function () {
      showMoreResults(block_li);
      showmore.style.display = "none";
      showless.style.display = "block";
    });

    showless.addEventListener("click", function () {
      showLessResults(block_li, toshow);
      showmore.style.display = "block";
      showless.style.display = "none";
    });
  }
}

/**
 * Show all the results.
 *
 * @return {void}
 */
const showMoreResults = (block_li) => {
  for (let i = 0, j = block_li.length; i < j; i++) {
    block_li[i].style.display = "block";
  }
};

/**
 * Show less results.
 *
 * @param {number} toshow Number of results to show.
 * @return {void}
 */
const showLessResults = (block_li, toshow) => {
  for (let i = 0, j = block_li.length; i < j; i++) {
    if (i > toshow - 1 && toshow !== 0) {
      block_li[i].style.display = "none";
    }
  }
};
