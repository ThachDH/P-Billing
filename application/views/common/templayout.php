<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<link href="<?=base_url('assets/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css');?>" rel="stylesheet" />
<link href="<?=base_url('assets/vendors/bootstrap-select/dist/css/bootstrap-select.min.css');?>" rel="stylesheet" />
<link href="<?=base_url('assets/vendors/jquery-confirm/jquery-confirm.min.css');?>" rel="stylesheet" />
<link href="<?=base_url('assets/css//ebilling.css');?>" rel="stylesheet" />

<style>
	.m-row-selected{
		background: violet;
	}
	.MT-toggle, .PY-toggle{
		display: none;
	}
	.MT-toggle button, .PY-toggle button {
		background-color: #fff!important;
	}
	.form-group{
		margin-bottom: .5rem!important;
	}
	.grid-hidden{
		display: none;
	}

	.modal-dialog-mw-py   {
		position: fixed;
		top:20%;
		margin: 0;
		width: 100%;
		height: 100%;
		padding: 0;
		max-width: 100%!important;
	}

	.modal-dialog-mw-py .modal-body{
		width: 90%!important;
		margin: auto;
	}

	.unchecked-Salan{
		pointer-events: none;
	}
	span.col-form-label {
		width: 70%;
		border-bottom: dotted 1px;
		display: inline-block;
		word-wrap: break-word;
	}

	#INV_DRAFT_TOTAL span.col-form-label{
		width: 64%;
		border-bottom: dotted 1px;
		display: inline-block;
		word-wrap: break-word;
	}
</style>
<div class="row" style="font-size: 12px!important;">
	<div class="col-xl-12">
		<div class="ibox collapsible-box">
			<i class="la la-angle-double-up dock-right"></i>
			<div class="ibox-head">
				<div class="ibox-title">LỆNH RÚT HÀNG CONTAINER</div>
			</div>
			<div class="ibox-body pt-3 pb-2 bg-f9 border-e">
				<section
				    <div id="canvas-container">
					    <canvas id="canvas" width="1360" height="810">
						    Your browser does not support HTML5.
					    </canvas>
				    </div>
				</section>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		init();
	});
</script>

<script type="text/javascript">
	var WIDTH; 					        // Width of the canvas
	var HEIGHT; 					    // Height of the canvas
	var CANVAS_RIGHT = 800;
	var CANVAS_LEFT = 9;
	var CANVAS_TOP = 9;
	var CANVAS_BOTTOM = 800;
	var INTERVAL = 20; 				    // How often to redraw the canavas (ms)

	var TILE_WIDTH = 42;                // SThe width of each tile
	var TILE_HEIGHT = 28;               // The height of each tile
	var TILE_RADIUS = 2;                // The radius of the rounded edges of the tiles		
	var TILE_TOTAL_WIDTH;               // The total width of each tile
	var TILE_TOTAL_HEIGHT;              // The total height of each tile	
	var TILE_DROP_OFFSET = 10;			// Used to drop the tile in the correct slot
	var TILE_FILL = '#F8D9A3';          // The background color of each tile
	var TILE_STROKE = '#000000';        // The border color of each tile
	var TILE_SELECTED_FILL = '#FF0000'; // The background color of selected tiles
	var TILE_TEXT_FILL = '#000000';     // The color of the text on the tile

	var canvas;                         // Reference to the canvas element
	var ctx;                            // Reference to the context used for drawing
	var isDragging = false;             // Indicating whether or not the user is dragging a tile
	var mouseX; 					    // Current mouse X coordinate
	var mouseY;                         // Current mouse Y coordinate
	var lastMouseX = 0;                 // The last seen mouse X coordinate
	var lastMouseY = 0;                 // the last seen mouse Y coordinate
	var changeInX;                      // The difference between the last and current mouse X coordinate
	var changeInY;                      // The difference between the last and current mouse Y coordinate

	var isSelecting = false;            // Indicates whether or not the user is drawing a selection rectangle
	var selectionStartX;                // Stores the x coordinate of where the user started drawing the selection rectangle
	var selectionStartY;                // Stores the y coordinate of where the user started drawing the selection rectangle
	var selectionEndX;                  // Stores the x coordinate of the end of the current selection rectangle
	var selectionEndY;                  // Stores the y coordinate of the end of the current selection rectangle

	var topmostTile;                    // Stores the topmost tile in the selected group
	var bottommostTile;                 // Stores the bottommost tile in the selected group
	var leftmostTile;                   // Stores the leftmost tile in the selected group
	var rightmostTile;                  // Stores the rightmost tile in the selected group
		
	var offRightX;						// Stores the x cooridnate of the mouse when a tile hits the right border
	var offLeftX;						// Stores the x cooridnate of the mouse when a tile hits the left border
	var offTopY;						// Stores the y cooridnate of the mouse when a tile hits the top border
	var offBottomY;						// Stores the y cooridnate of the mouse when a tile hits the bottom border

	var redrawCanvas = false;           // Indicates whether or not the canvas needs to be redrawn	

	var tilesInPlay = [];               // Stores all tiles currently on the canvas
	var tiles = [];                     // Stores the tiles not currently on the canvas 	

	var offX;                           // Indicates that the mouse has moved off the canvas
										// on the x axis
	var offY                            // Indicates that the mouse has moved off the canvas
										// on the y axis

	// Object to represent each tile in the game
	function Tile() {
		this.x = 0;
		this.y = 0;
		this.letter = '';
		this.value = 0;
		this.selected = false;
	}

	function init() {				
		// Setup the global variables
		TILE_TOTAL_WIDTH = TILE_WIDTH + TILE_RADIUS;
		TILE_TOTAL_HEIGHT = TILE_HEIGHT + TILE_RADIUS;

		canvas = document.getElementById('canvas');

		var ctxContainer = document.getElementById("canvas-container").offsetWidth;
		canvas.setAttribute("width", ctxContainer);

		HEIGHT = canvas.height;
		WIDTH = canvas.width;
		ctx = canvas.getContext('2d');
		
		// Set the values of the x and y mouse coordinates used when
		// a tile hits the border to values outside of the canvas
		offRightX = -1;
		offLeftX = WIDTH + 1;
		offTopY = HEIGHT + 1;
		offBottomY = -1;
			
		// Set the global text properties for the text drawn on the letters
		ctx.font = '20px sans-serif';
		ctx.textBaseline = 'top';		

		// Set how often the draw method will be called
		setInterval(draw, 20);

		// Wire up the mouse event handlers
		canvas.onmousedown = mouseDown;
		document.onmouseup = mouseUp;	            

		// Setup the tile arrays
		//initTiles();

		// Add 21 tiles at the bottom of the canvas
		var y = 60;
		var x = 60;

		for( var j = 0; j <= 2; j++ ){
			for (var i = 0; i < 23; i++) {
				addTile(x, y, i);
				x = x + TILE_TOTAL_WIDTH + 5;
			}
			x = 60;
			y = y + TILE_TOTAL_HEIGHT + 5;
		}
		
	}

	function initTiles() {
		// Create a new tile object for each letter and value above
		for (var i = 0; i < possibleLetters.length; i++) {
			var tile = new Tile;
			tile.letter = possibleLetters[i];
			tile.value = values[i];

			// Add the tile to the tiles array
			tiles.push(tile);
		}
	}

	// Adds a random tile to the canvas at the given coordinates
	function addTile(x, y, i) {
		// Get a random number the be used to index into
		// the tiles array
		//var index = Math.floor(Math.random() * tiles.length);

		// Remove the random tile from the array and
		// set its location
		var tile = new Tile;
		//tile.letter = i;
		tile.x = x;
		tile.y = y;

		// Add the tile to the tilesInPlay array and
		// indicate taht the canvas needs to be redrawn
		tilesInPlay.push(tile);
		needsRedraw();
	}

	// Indicate that the canvas needs to be redrawn
	function needsRedraw() {
		redrawCanvas = true;
	}

	// Draw the various objects on the canvas
	function draw() {
		// Only draw the canvas if it is not valid
		if (redrawCanvas) {
			clear(ctx);

			// draw the unselected tiles first so they appear under the selected tiles
			for (var i = 0; i < tilesInPlay.length; i++) {
				if (!tilesInPlay[i].selected)
					drawTile(ctx, tilesInPlay[i]);
			}

			// now draw the selected tiles so they appear on top of the unselected tiles
			for (var i = 0; i < tilesInPlay.length; i++) {
				if (tilesInPlay[i].selected)
					drawTile(ctx, tilesInPlay[i]);
			}	

			// If the user is drawing a selection rectangle, draw it
            if (isSelecting) {
                drawSelectionRectangle(ctx);
			}
			// Indicate that the canvas no longer needs to be redrawn
			redrawCanvas = false;
		}
	}

	// Draw a single tile using the passed in context
	function drawTile(context, tile) {
		// Draw the tile with rounded corners
		context.beginPath();
		
		context.moveTo(tile.x + TILE_RADIUS, tile.y);
		
		//line top
		context.lineTo(tile.x + TILE_WIDTH - TILE_RADIUS, tile.y);
		
		// radius top right
		context.quadraticCurveTo(tile.x + TILE_WIDTH, tile.y, tile.x + TILE_WIDTH, tile.y + TILE_RADIUS);
	
		//line right
		context.lineTo(tile.x + TILE_WIDTH, tile.y + TILE_HEIGHT - TILE_RADIUS);
		//radius bottom right
		context.quadraticCurveTo(tile.x + TILE_WIDTH, tile.y + TILE_HEIGHT, tile.x + TILE_WIDTH - TILE_RADIUS, tile.y + TILE_HEIGHT);
		
		//line bottom
		context.lineTo(tile.x + TILE_RADIUS, tile.y + TILE_HEIGHT);
	
		//radius bottom left
		context.quadraticCurveTo(tile.x, tile.y + TILE_HEIGHT, tile.x, tile.y + TILE_HEIGHT - TILE_RADIUS);
		//line left
		context.lineTo(tile.x , tile.y + TILE_RADIUS);
		
		context.quadraticCurveTo(tile.x, tile.y, tile.x + TILE_RADIUS, tile.y);
		context.closePath();

		// Draw the border around the tile
		context.strokeStyle = TILE_STROKE;
		context.stroke();	            

		// Fill the tile background depending on whether or not
		// the tile is selected or not
		context.fillStyle = (tile.selected ? TILE_SELECTED_FILL : TILE_FILL);
		context.fill();	            	            

		// Draw the letter on the tile
		context.fillStyle = TILE_TEXT_FILL;                

		// Get the text metrics so we can measure the width of the letter
		// that will be drawn
		var textMetrics = context.measureText(tile.letter);

		// Draw the letter in the middle of the tile
		context.fillText(tile.letter, tile.x + ((TILE_TOTAL_WIDTH - textMetrics.width - 2) / 2), tile.y + 2);
	}

	// Draws the selection rectangle
	function drawSelectionRectangle(context) {
		context.strokeStyle = TILE_STROKE;

		// Figure out the top left corner of the rectangle
		var x = Math.min(selectionStartX, selectionEndX);
		var y = Math.min(selectionStartY, selectionEndY);

		// Calculate the width and height of the rectangle
		var width = Math.abs(selectionEndX - selectionStartX);
		var height = Math.abs(selectionEndY - selectionStartY);

		// Draw the rectangle
		context.strokeRect(x, y, width, height);
	}	 
	
	// Clears the canvas
	function clear(c) {
		c.clearRect(0, 0, WIDTH, HEIGHT);
	}

	function mouseDown(e) {
		// Get the current mouse coordinates
		getMouse(e);

		// Indicate that the user is not dragging any tiles
		isDragging = false;

		// Check to see if the user as clicked a tile
		for (var i = 0; i < tilesInPlay.length; i++) {
			var tile = tilesInPlay[i];

			// Calculate the left, right, top and bottom
			// bounds of the current tile
			var left = tile.x;
			var right = tile.x + TILE_TOTAL_WIDTH;
			var top = tile.y;
			var bottom = tile.y + TILE_TOTAL_HEIGHT;

			// Determine if the tile was clicked
			if (mouseX >= left && mouseX <= right && mouseY >= top && mouseY <= bottom) {																									
				// If the user selected a tile that was not selected before,
				// clear all selected tiles
				if (!tilesInPlay[i].selected) {
					clearSelectedTiles();
					topmostTile = bottommostTile = leftmostTile = rightmostTile = tilesInPlay[i];
				}
					
				// Indicate that the current tile is selected
				tilesInPlay[i].selected = true;					
				isDragging = true;

				// Wire up the onmousemove event to handle the dragging
				document.onmousemove = mouseMove;
				needsRedraw();
				return;
			}
		}

		// No tiles were clicked, make sure all tiles are not selected
		clearSelectedTiles();
					
		// Indicate that the user is drawing a selection rectangle and
		// update the selection rectangle start and edit coordinates
		isSelecting = true;	
		selectionStartX = mouseX;
		selectionStartY = mouseY;
		selectionEndX = mouseX;
		selectionEndY = mouseY;
		
		// Wire up the onmousemove event so we can dynamically draw the rectangle
		document.onmousemove = mouseMove;	           
		needsRedraw();			
	}

	function mouseMove(e) {
		// If the user is dragging a tile
		if (isDragging) {
			getMouse(e);

			for (var i = 0; i < tilesInPlay.length; i++) {
				var tile = tilesInPlay[i];
				
				// Only if the tile is selected do we want to drag it
				if (tile.selected) {	                   

					// Only move tiles to the right or left if the mouse is between the left and 
					// right bounds of the canvas
					if (mouseX < CANVAS_RIGHT && mouseX > CANVAS_LEFT) {

						// Move the tile if the rightmost or leftmost tile of the group is not off the canvas 
						// or if the the right or leftmost tiles hit the one of the borders previously and the
						// mouse X coordinate has now passed the stored X coordinate when the tile hit the border
						if ((rightmostTile.x + TILE_TOTAL_WIDTH <= WIDTH && leftmostTile.x >= 0) || (mouseX <= offRightX) || (mouseX >= offLeftX)) {
							tile.x = tile.x + changeInX;	                               
						}
					}	                   

					// Only move tiles up or down if the mouse is between the top and bottom
					// bounds of the canvas
					if (mouseY < CANVAS_BOTTOM && mouseY > CANVAS_TOP) {

						// Move the tile if the topmost or bottommost tile of the group is not off the canvas and the
						// or if the the top or bottommost tiles hit the one of the borders previously and the
						// mouse Y coordinate has now passed the stored Y coordinate when the tile hit the border
						if ((topmostTile.y >= 0 && bottommostTile.y + TILE_TOTAL_HEIGHT <= HEIGHT) || (mouseY <= offBottomY) || (mouseY >= offTopY)) {
							tile.y = tile.y + changeInY;
						}
					}                      
				}
			}

			// If offRightX is less than zero, meaning that the rightmostTile has not
			// hit the right border of the canvas since our last mouseMove call, and the 
			// now the rightmostTile has hit the right border, set the offRightX variable
			// to the current X coordinate of the mouse.  This will be used on the next
			// call to mouseMove to ensure the tiles are not dragged off the canvas.
			// Otherwise set offRightX to -1 indicating that the tiles are not being
			// dragged off the canvas.
			if (offRightX < 0 && (rightmostTile.x + TILE_TOTAL_WIDTH) >= WIDTH)
				offRightX = mouseX;
			else if (mouseX <= offRightX)
				offRightX = -1;

			// Same as above but for left border
			if (offLeftX > WIDTH && (leftmostTile.x <= 0))
				offLeftX = mouseX;
			else if (mouseX >= offLeftX)
				offLeftX = WIDTH + 1;

			// Same as above but for bottom border
			if (offBottomY < 0 && (bottommostTile.y + TILE_TOTAL_HEIGHT) >= HEIGHT)
				offBottomY = mouseY;
			else if (mouseY <= offBottomY)
				offBottomY = -1;
			
			// Same as above but for top border
			if (offTopY > HEIGHT && (topmostTile.y <= 0))
				offTopY = mouseY;
			else if (mouseY >= offTopY)
				offTopY = HEIGHT + 1;	                                 

			needsRedraw();
		}		

		// Update the end coordinates of the selection rectangle
		if (isSelecting) {
			getMouse(e);

			selectionEndX = mouseX;
			selectionEndY = mouseY;	           
		
			needsRedraw();
		}
	}

	function mouseUp(e) {

		// Indicate that we are no longer dragging tiles and stop
		// handling mouse movement
		isDragging = false;
		document.onmousemove = null;

		// Drop the tile in the closest slot	       
		for (var i = 0; i < tilesInPlay.length; i++) {
			var tile = tilesInPlay[i];
			
			// Only move the tile if it is currently selected
			if (tile.selected) {
				// Mod-ing the current x and y coordinates by the width
				// and height of the tile will give us the distance
				// the tile is from the left and top border's of the
				// slot the tile's x and y coordinates lie
				var offsetX = ((tile.x) % TILE_TOTAL_WIDTH);
				var offsetY = ((tile.y) % TILE_TOTAL_HEIGHT);

				// If the offsetX is within the defined distance
				// from the left border of the slot to the right, 
				// update offsetX to move the tile to the slot to the right
				if (offsetX >= (TILE_TOTAL_WIDTH - TILE_DROP_OFFSET))
					offsetX = offsetX - TILE_TOTAL_WIDTH; 
					
				// If the offsetY is within the defined distance from
				// the top border of the slot below, update offsetY
				// to move the tile to the slot below
				if (offsetY >= (TILE_TOTAL_WIDTH - TILE_DROP_OFFSET))
					offsetY = offsetY - TILE_TOTAL_HEIGHT;

				// Update the tile's x and y coordinates to drop it
				// into a slot.  Note that if either of the above
				// conditions were true, the offset will be a negative
				// number thus moving the tile to the right or down
				tile.x = tile.x - offsetX;
				tile.y = tile.y - offsetY;

				moveToEmptySlot(tile);                   
				needsRedraw();
			}
		}               
			
		// Deselect all tiles
		clearSelectedTiles();
		
		if (isSelecting) {		
			// Mark the tiles in the drawn rectangle as selected
			selectTilesInRectangle();
			
			// Reset the selection rectangle
			isSelecting = false;
			selectionStartX = 0;
			selectionStartY = 0;
			selectionEndX = 0;
			selectionEndY = 0;				
		}
		
		needsRedraw();
	}
			
	// Ensures that the passed in tile is not stacked on top of another tile
	function moveToEmptySlot(tile) {	           
		var count = 0;
		var slotsToMove = 1;

		// We multiple the tile width and heigth times these
		// values in order to get the tile to move the correct
		// direction
		// [ Up, Right, Left, Down ]
		var xMultipliers = [0, 1, 0, -1]
		var yMultipliers = [-1, 0, 1, 0]

		// Each iteration of this loop will move the tile the needed
		// number of slots before we need to change directions again
		while (!isInEmptySlot(tile)) {
			// The slotsToMove variable indicates how many
			// slots we need to move in the current direction
			// before we need to turn a corner
			for (var i = 0; i < slotsToMove; i++) {
				// Move the tile in the current direction
				tile.x += xMultipliers[count % 4] * TILE_TOTAL_WIDTH;
				tile.y += yMultipliers[count % 4] * TILE_TOTAL_HEIGHT;
				needsRedraw();

				// Check to see if the tile is in an empty slot now
				if (isInEmptySlot(tile)) {
					break;
				}
			}

			count = count + 1;

			// If count % 2 == 0 then we need to increase the
			// number of slots the tile should be moved in the 
			// next direction the next round                        
			if (count % 2 == 0)
				slotsToMove = slotsToMove + 1;
		}
	}

	function isInEmptySlot(tile) {

		// If the tile is off the canvas, then return that it is not in an empty slot
		if (tile.x < 0 || tile.x + TILE_TOTAL_WIDTH > WIDTH || tile.y < 0 || tile.y + TILE_TOTAL_HEIGHT > HEIGHT) {
			return false;
		}

		// Check to see if there is another tile on the canvas with the same coordinates                
		for (var i = 0; i < tilesInPlay.length; i++) {
			var otherTile = tilesInPlay[i];

			// If we are comparing two different tiles and they have the same x and y values 
			// then return false indicating that we are not in an an empty slot
			if (otherTile != tile && tile.x == otherTile.x && tile.y == otherTile.y) {
				return false;
			}
		}

		return true;
	}
	
	// Selects all the tiles is in the user dragged rectangle
	function selectTilesInRectangle() {
		
		// Get the bounds of the drawn rectangle
		var selectionTop = Math.min(selectionStartY, selectionEndY);
		var selectionBottom = Math.max(selectionStartY, selectionEndY);
		var selectionLeft = Math.min(selectionStartX, selectionEndX);
		var selectionRight = Math.max(selectionStartX, selectionEndX);

		// Loop through all the tiles and select the tile if it lies within the 
		// bounds of the rectangle
		for (var i = 0; i < tilesInPlay.length; i++) {
			var tile = tilesInPlay[i];	            

			var tileTop = tile.y;
			var tileBottom = tile.y + TILE_TOTAL_HEIGHT;
			var tileLeft = tile.x;
			var tileRight = tile.x + TILE_TOTAL_WIDTH;

			tile.selected = (tileTop >= selectionTop && tileBottom <= selectionBottom && tileLeft >= selectionLeft && tileRight <= selectionRight);				
		}	

		// Get the top, bottom, left, and rightmost tiles
		getExtremeTiles();
	}
	
	// Finds the top, bottom, left, and rightmost tiles of the selected group
	function getExtremeTiles() {	            
		for (var i = 0; i < tilesInPlay.length; i++) {
			var tile = tilesInPlay[i];
	 
			if (tile.selected) {	                
				if (topmostTile == null || tile.y < topmostTile.y)
					topmostTile = tile;

				if (bottommostTile == null || tile.y > bottommostTile.y)
					bottommostTile = tile;

				if (leftmostTile == null || tile.x < leftmostTile.x)
					leftmostTile = tile;

				if (rightmostTile == null || tile.x > rightmostTile.x)
					rightmostTile = tile;
			}	           
		}
	}

	// Sets the tile.selected property to false for
	// all tiles in play and clears all extreme tiles
	function clearSelectedTiles() {
		for (var i = 0; i < tilesInPlay.length; i++) {
			tilesInPlay[i].selected = false;
		}
		
		// Clear the exterme tiles
		topmostTile = null;
		bottommostTile = null;
		leftmostTile = null;
		rightmostTile = null;
	}

	// Sets mouseX and mouseY variables taking into account padding and borders
	function getMouse(e) {
		var element = canvas;
		var offsetX = 0;
		var offsetY = 0;

		// Calculate offsets
		if (element.offsetParent) {
			do {
				offsetX += element.offsetLeft;
				offsetY += element.offsetTop;
			} while ((element = element.offsetParent));
		}	

		// Calculate the mouse location
		mouseX = e.pageX - offsetX;
		mouseY = e.pageY - offsetY;

		// Calculate the change in mouse position for the last
		// time getMouse was called
		changeInX = mouseX - lastMouseX;
		changeInY = mouseY - lastMouseY;

		// Store the current mouseX and mouseY positions
		lastMouseX = mouseX;
		lastMouseY = mouseY;
	}	
</script>
<script src="<?=base_url('assets/vendors/moment/min/moment.min.js');?>"></script>
<script src="<?=base_url('assets/vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js');?>"></script>
<script src="<?=base_url('assets/vendors/bootstrap-select/dist/js/bootstrap-select.min.js');?>"></script>
<script src="<?=base_url('assets/vendors/jquery-confirm/jquery-confirm.min.js');?>"></script>
<!--format number-->
<script src="<?=base_url('assets/js/jshashtable-2.1.js');?>"></script>
<script src="<?=base_url('assets/js/jquery.numberformatter-1.2.3.min.js');?>"></script>