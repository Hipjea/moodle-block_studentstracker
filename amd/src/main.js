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

/**
 * Init script.
 *
 * @param {object} root The root element for studentstracker.
 * @return {void}
 */
export const init = (root) => {
  const toShow = parseInt(root.dataset.show, 10);
  const blockLi = root.querySelectorAll("#studentstracker-list > li");
  const showMore = root.querySelector('[data-action="show-more"]');
  const showLess = root.querySelector('[data-action="show-less"]');

  if (!showMore || !showLess) {
    return;
  }

  if (toShow > 0 && toShow < blockLi.length) {
    showMore.addEventListener("click", () => {
      showMoreResults(blockLi);
      showMore.hidden = true;
      showLess.hidden = false;
    });

    showLess.addEventListener("click", () => {
      showLessResults(blockLi, toShow);
      showMore.hidden = false;
      showLess.hidden = true;
    });
  }
};

/**
 * Toggle the hidden attribute.
 *
 * @param {HTMLLIElement} blockLi The <li> element to manipulate.
 * @return {void}
 */
const showMoreResults = (blockLi) => {
  blockLi.forEach((li) => {
    li.hidden = false;
  });
};

/**
 * Toggle the hidden attribute.
 *
 * @param {HTMLLIElement} blockLi The <li> element to manipulate.
 * @param {number} toShow Number of results to show.
 * @return {void}
 */
const showLessResults = (blockLi, toShow) => {
  blockLi.forEach((li, index) => {
    li.hidden = toShow > 0 && index >= toShow;
  });
};
