// Toggle comments section
function toggleComments(postId) {
    const commentsSection = document.getElementById(`comments-${postId}`);
    const commentInput = document.getElementById(`comment-input-${postId}`);
    
    if (commentsSection.style.display === 'none' || commentsSection.style.display === '') {
        commentsSection.style.display = 'block';
        commentsSection.classList.add('show');
        
        // Focus on input after a short delay to allow animation
        setTimeout(() => {
            commentInput.focus();
        }, 100);
    } else {
        commentsSection.style.display = 'none';
        commentsSection.classList.remove('show');
    }
}

// Post comment function
function postComment(postId) {
    const commentInput = document.getElementById(`comment-input-${postId}`);
    const commentsList = document.getElementById(`comments-list-${postId}`);
    const commentText = commentInput.value.trim();
    
    if (commentText === '') {
        alert('Please enter a comment');
        return;
    }
    
    // Create new comment element
    const commentItem = document.createElement('div');
    commentItem.className = 'comment-item';
    
    const currentTime = new Date();
    const timeString = 'Just now';
    
    commentItem.innerHTML = `
        <div class="comment-avatar">U</div>
        <div class="comment-content">
            <div class="comment-header">
                <span class="comment-author">Current User</span>
                <span class="comment-time">${timeString}</span>
            </div>
            <div class="comment-text">${commentText}</div>
        </div>
    `;
    
    // Add to comments list
    commentsList.appendChild(commentItem);
    
    // Clear input
    commentInput.value = '';
    
    // Here you would typically send the comment to your PHP backend
    // Example AJAX call:
    /*
    fetch('your_php_endpoint.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            post_id: postId,
            comment: commentText
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Comment posted successfully');
    })
    .catch(error => {
        console.error('Error posting comment:', error);
    });
    */
}

// Handle Enter key in textarea
document.addEventListener('keydown', function(e) {
    if (e.target.classList.contains('comment-input')) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            
            // Get post ID from the textarea ID
            const postId = e.target.id.split('-')[2];
            postComment(postId);
        }
    }
});

// Auto-resize textarea
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('comment-input')) {
        e.target.style.height = 'auto';
        e.target.style.height = Math.min(e.target.scrollHeight, 120) + 'px';
    }
});

// Placeholder functions for other actions
function toggleLike(postId) {
    console.log('Toggle like for post:', postId);
    // Implement like functionality
}

function sharePost(postId) {
    console.log('Share post:', postId);
    // Implement share functionality
}
