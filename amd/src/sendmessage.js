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

import Ajax from "core/ajax";
import Modal from "core/modal";
import Notification from "core/notification";
import { getString } from "core/str";

export default class {
  static init(courseID) {
    const button = document.querySelector("#studentstracker-send-button");

    if (!button) {
      return;
    }

    button.addEventListener("click", async (e) => {
      e.preventDefault();

      const content = document.querySelector(
        "#studentstracker-message-content"
      );

      const cancelStr = await getString("cancel", "core");
      const sendMessageStr = await getString(
        "sendmessage",
        "block_studentstracker"
      );

      const modal = await Modal.create({
        title: sendMessageStr,
        body: `
            <div class="form-group">
                <label for="studentstracker-message">
                    ${sendMessageStr}
                </label>
                <textarea 
                    id="studentstracker-message"
                    class="form-control"
                    rows="8">${content.textContent.trim()}</textarea>
            </div>
        `,
        footer: `
            <button type="button" class="btn btn-secondary" data-action="cancel">
                ${cancelStr}
            </button>
            <button type="button" class="btn btn-primary studentstracker-send">
                ${sendMessageStr}
            </button>
        `,
        show: true
      });

      modal.getModal()[0].addEventListener("click", (event) => {
        const sendButton = event.target.closest(".studentstracker-send");

        if (!sendButton) {
          return;
        }

        const message = modal
          .getBody()[0]
          .querySelector("#studentstracker-message").value;

        const request = {
          methodname: "block_studentstracker_send_message",
          args: {
            courseid: parseInt(courseID),
            message: message
          }
        };

        Ajax.call([request])[0].done().fail(Notification.exception);

        modal.destroy();
      });
    });
  }
}
