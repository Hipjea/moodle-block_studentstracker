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
 * @author     Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const arrowDown = `<svg
  width="18"
  height="11"
  viewBox="0 0 1792 1095.1111"
  version="1.1"
  xmlns="http://www.w3.org/2000/svg"
  xmlns:svg="http://www.w3.org/2000/svg">
  <path
    fill="#FFFFFF"
    d="m 1685.1961,209.41181 -166,-165.000053 q -19,-19 -45,-19 -26,0 -45,19 l 
    -531.00002,531.000053 -531,-531.000053 q -19,-19 -45,-19 -26,0 -45,19 l 
    -166,165.000053 q -18.999997,19 -18.999997,45.5 0,26.5 18.999997,45.5 l 
    742,740.99999 q 19,19 45,19 26,0 45,-19 L 1685.1961,300.41181 q 19,-19 19,
    -45.5 0,-26.5 -19,-45.5 z"
    id="path912"
    style="fill:#ffffff;fill-opacity:1" />
  </svg>`;
const arrowUp = `<svg
  width="18"
  height="11"
  viewBox="0 0 1792 1095.1111"
  version="1.1"
  xmlns="http://www.w3.org/2000/svg"
  xmlns:svg="http://www.w3.org/2000/svg">
  <path
    fill="#FFFFFF"
    d="m 1685.1961,876.41175 -166,165.00005 q -19,19 -45,19 -26,0 -45,-19 l 
    -531.00002,-531.00005 -531,531.00005 q -19,19 -45,19 -26,0 -45,-19 l 
    -166,-165.00005 q -18.999997,-19 -18.999997,-45.5 0,-26.5 18.999997,
    -45.5 l 742,-740.999993 q 19,-19 45,-19 26,0 45,19 L 1685.1961,785.41175 
    q 19,19 19,45.5 0,26.5 -19,45.5 z"
    id="path912"
    style="fill:#ffffff;fill-opacity:1" />
  </svg>`;

/**
 * Init script.
 *
 * @param {object} root The root element for studentstracker.
 * @return {void}
 */
export const init = (root) => {
  const toshow = parseInt(root.dataset.show);
  const block_li = root.querySelectorAll("li");

  for (let i = 0, j = block_li.length; i < j; i++) {
    if (i > toshow - 1 && toshow !== 0) {
      block_li[i].style.display = "none";
    }
  }

  if (toshow > 0 && toshow < block_li.length) {
    const showmore = document.querySelector("#tracker_showmore"),
      btn = document.createElement("button"),
      showless = document.querySelector("#tracker_showless"),
      btnless = document.createElement("button");

    btn.innerHTML = arrowDown;
    btnless.innerHTML = arrowUp;
    showmore.appendChild(btn);
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
};

/**
 * Show all the results.
 *
 * @param {HTMLLIElement} block_li The <li> element to manipulate.
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
 * @param {HTMLLIElement} block_li The <li> element to manipulate.
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
