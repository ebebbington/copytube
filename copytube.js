/* This script handles events */

/* global $ */
'use strict'

// Importing function from functions.js so it can be used in this script without error
import { getVideosAndComments, getUsername, validateInput } from 'functions.js' // fixme :: unexpected {

$(document).ready(function () {
  // Run username function
  getUsername('', 'false')
  // Comment character count
  $(document).on('keyup', '#comment-bar', function () {
    const commentLength = $('#comment-bar').val().length
    $('#comment-count').text(commentLength)
  })
  // todo :: Removing Drop-down for Search in Prep for Auto-complete
  /* var drop_down = true;
  $(document).on('keyup', '#search-bar',function(){
      disable drop-down elements
      drop_down = false;
      if (drop_down == false){
          $('.dropdown-content').prop('textContent', "");
      }
  }) */
  // On Click of Add Comment Button
  $('#comment-button').on('click', function () {
    // Run addComment function
    let comment = $('#comment-bar').val()
    const maxLength = 400
    validateInput(comment, 'addComment', maxLength)
  })
  // On click of the drodown content
  $(document).on('click', '.dropdown-titles', function () {
    let clickedTitle = $(this).text()
    validateInput(clickedTitle, 'videoSearch', 80)
  })
  // On Click of A Rabbit Hole Video
  $(document).on('click', '.rabbit-hole-videos', function () {
    let clickedVideoTitle = $(this).prop('title')
    getVideosAndComments(clickedVideoTitle)
  })
  // On Click of Search Button
  $(document).on('click', '#search-button', function () {
    // Validate input
    let input = $('#search-bar').val()
    validateInput(input, 'videoSearch', 80)
  })
})
