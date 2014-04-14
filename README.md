## UUID Helper

A helper module that assists in working with and enhancing the use of Universal Unique Identifiers in Drupal 8.

### Features

* Displays UUID information on core enabled entities. (e.g. node)
* Allows for entity paths to be accessed by specifying the entity UUID instead of that of the serial identifier (e.g. `node/[uuid]` instead of `node/[nid]`)
* Rewrites all outbound entity paths to be represented with the UUID instead of serial identifier (e.g. `node/[uuid]` instead of `node/[nid]`).
* Views argument for accepting entity identifiers, whether serial (e.g. a node's id) or a UUID.
* Views argument validator for validating an entity identifier (serial or uuid) with a valid entity.

### TODO

* Override path saving so that entities are saved using universal identifier instead of serial.
* Override menu creation (e.g. menu) so that menu links with UUIDs are preferred verse that which uses serial.
* Documentation
    * Write documentation on strategy for building with portability in mind while leveraging UUIDs.
* Tests

### License

The UUID Features Helper is licensed under the [GNU General Public License](http://gnu.org/licenses/gpl-2.0.html) version 2.
