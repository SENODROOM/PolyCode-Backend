/*
 * File: signal_handling.c
 * Description: Signal handling and process monitoring
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <signal.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <time.h>
#include <errno.h>

// Global variables for signal handling
volatile sig_atomic_t signal_received = 0;
volatile sig_atomic_t interrupt_count = 0;
volatile sig_atomic_t terminate_count = 0;
volatile sig_atomic_t alarm_count = 0;

// Signal handler structure
typedef struct {
    int signal_number;
    const char* name;
    void (*handler)(int);
} SignalHandler;

// Process monitoring structure
typedef struct {
    pid_t pid;
    char name[256];
    time_t start_time;
    int status;
    int exit_code;
} ProcessInfo;

// Signal handler functions
void handle_sigint(int sig) {
    signal_received = SIGINT;
    interrupt_count++;
    printf("\nReceived SIGINT (Ctrl+C) - Count: %d\n", interrupt_count);
    
    if (interrupt_count >= 3) {
        printf("Too many interrupts, exiting...\n");
        exit(EXIT_SUCCESS);
    }
}

void handle_sigterm(int sig) {
    signal_received = SIGTERM;
    terminate_count++;
    printf("Received SIGTERM - Count: %d\n", terminate_count);
}

void handle_sigalrm(int sig) {
    alarm_count++;
    printf("Alarm triggered! Count: %d\n", alarm_count);
}

void handle_sigusr1(int sig) {
    printf("Received SIGUSR1 - Custom signal\n");
}

void handle_sigusr2(int sig) {
    printf("Received SIGUSR2 - Another custom signal\n");
}

void handle_sigchld(int sig) {
    int status;
    pid_t pid;
    
    while ((pid = waitpid(-1, &status, WNOHANG)) > 0) {
        if (WIFEXITED(status)) {
            printf("Child process %d exited with status %d\n", pid, WEXITSTATUS(status));
        } else if (WIFSIGNALED(status)) {
            printf("Child process %d killed by signal %d\n", pid, WTERMSIG(status));
        } else if (WIFSTOPPED(status)) {
            printf("Child process %d stopped by signal %d\n", pid, WSTOPSIG(status));
        }
    }
}

void handle_sighup(int sig) {
    printf("Received SIGHUP - Hangup signal (reloading configuration)\n");
}

// Setup signal handlers
void setup_signal_handlers() {
    struct sigaction sa;
    
    // Setup SIGINT handler
    sa.sa_handler = handle_sigint;
    sigemptyset(&sa.sa_mask);
    sa.sa_flags = SA_RESTART;
    sigaction(SIGINT, &sa, NULL);
    
    // Setup SIGTERM handler
    sa.sa_handler = handle_sigterm;
    sigaction(SIGTERM, &sa, NULL);
    
    // Setup SIGALRM handler
    sa.sa_handler = handle_sigalrm;
    sigaction(SIGALRM, &sa, NULL);
    
    // Setup SIGUSR1 handler
    sa.sa_handler = handle_sigusr1;
    sigaction(SIGUSR1, &sa, NULL);
    
    // Setup SIGUSR2 handler
    sa.sa_handler = handle_sigusr2;
    sigaction(SIGUSR2, &sa, NULL);
    
    // Setup SIGCHLD handler
    sa.sa_handler = handle_sigchld;
    sigaction(SIGCHLD, &sa, NULL);
    
    // Setup SIGHUP handler
    sa.sa_handler = handle_sighup;
    sigaction(SIGHUP, &sa, NULL);
    
    // Ignore SIGPIPE
    signal(SIGPIPE, SIG_IGN);
    
    printf("Signal handlers setup complete\n");
}

// Signal set operations
void demonstrate_signal_sets() {
    printf("\n=== Signal Set Operations ===\n");
    
    sigset_t set;
    
    // Initialize empty signal set
    sigemptyset(&set);
    printf("Empty signal set created\n");
    
    // Add signals to set
    sigaddset(&set, SIGINT);
    sigaddset(&set, SIGTERM);
    sigaddset(&set, SIGUSR1);
    printf("Added SIGINT, SIGTERM, SIGUSR1 to set\n");
    
    // Check if signal is in set
    if (sigismember(&set, SIGINT)) {
        printf("SIGINT is in the set\n");
    }
    
    // Remove signal from set
    sigdelset(&set, SIGINT);
    printf("Removed SIGINT from set\n");
    
    if (!sigismember(&set, SIGINT)) {
        printf("SIGINT is no longer in the set\n");
    }
    
    // Block signals
    printf("Blocking SIGTERM and SIGUSR1...\n");
    sigprocmask(SIG_BLOCK, &set, NULL);
    
    // Send signals to self (they will be blocked)
    raise(SIGTERM);
    raise(SIGUSR1);
    printf("Signals sent (blocked)\n");
    
    // Unblock signals
    printf("Unblocking signals...\n");
    sigprocmask(SIG_UNBLOCK, &set, NULL);
    
    // Wait a bit for signals to be delivered
    sleep(1);
}

// Alarm demonstration
void demonstrate_alarms() {
    printf("\n=== Alarm Demonstration ===\n");
    
    // Set one-shot alarm
    printf("Setting 3-second alarm...\n");
    alarm(3);
    
    // Wait for alarm
    sleep(4);
    
    // Set periodic alarm using setitimer
    printf("Setting periodic alarm (every 2 seconds)...\n");
    
    struct itimerval timer;
    timer.it_interval.tv_sec = 2;
    timer.it_interval.tv_usec = 0;
    timer.it_value.tv_sec = 2;
    timer.it_value.tv_usec = 0;
    
    if (setitimer(ITIMER_REAL, &timer, NULL) == -1) {
        perror("setitimer");
        return;
    }
    
    // Let periodic alarm run for 10 seconds
    for (int i = 0; i < 5; i++) {
        printf("Main thread working... (%d/5)\n", i + 1);
        sleep(2);
    }
    
    // Stop periodic alarm
    timer.it_value.tv_sec = 0;
    timer.it_value.tv_usec = 0;
    setitimer(ITIMER_REAL, &timer, NULL);
    
    printf("Periodic alarm stopped\n");
}

// Process monitoring
void monitor_process(pid_t pid) {
    printf("\n=== Process Monitoring ===\n");
    printf("Monitoring process %d\n", pid);
    
    // Check if process exists
    if (kill(pid, 0) == -1) {
        if (errno == ESRCH) {
            printf("Process %d does not exist\n", pid);
        } else {
            perror("kill");
        }
        return;
    }
    
    // Monitor process for 10 seconds
    for (int i = 0; i < 10; i++) {
        if (kill(pid, 0) == -1) {
            if (errno == ESRCH) {
                printf("Process %d has terminated\n", pid);
                break;
            }
        }
        
        printf("Process %d is still running (check %d/10)\n", pid, i + 1);
        sleep(1);
    }
}

// Create child process with signal handling
void create_signal_child() {
    pid_t pid = fork();
    
    if (pid == 0) {
        // Child process
        printf("Child process %d started\n", getpid());
        
        // Setup signal handlers for child
        struct sigaction sa;
        sa.sa_handler = handle_sigusr1;
        sigemptyset(&sa.sa_mask);
        sa.sa_flags = SA_RESTART;
        sigaction(SIGUSR1, &sa, NULL);
        
        // Child does some work
        for (int i = 0; i < 10; i++) {
            printf("Child working... %d/10\n", i + 1);
            sleep(1);
        }
        
        printf("Child process %d finished\n", getpid());
        exit(EXIT_SUCCESS);
    } else if (pid > 0) {
        // Parent process
        printf("Parent created child %d\n", pid);
        
        // Send signals to child
        sleep(2);
        printf("Parent sending SIGUSR1 to child %d\n", pid);
        kill(pid, SIGUSR1);
        
        sleep(2);
        printf("Parent sending another SIGUSR1 to child %d\n", pid);
        kill(pid, SIGUSR1);
        
        // Wait for child to finish
        int status;
        waitpid(pid, &status, 0);
        
        printf("Child %d finished with status %d\n", pid, status);
    } else {
        perror("fork");
    }
}

// Process group demonstration
void demonstrate_process_groups() {
    printf("\n=== Process Group Demonstration ===\n");
    
    pid_t group_leader = fork();
    
    if (group_leader == 0) {
        // Group leader process
        setpgid(0, 0); // Create new process group
        printf("Group leader PID: %d, PGID: %d\n", getpid(), getpgrp());
        
        // Create child processes in the same group
        for (int i = 0; i < 3; i++) {
            pid_t child = fork();
            
            if (child == 0) {
                // Child process
                printf("Child %d: PID=%d, PGID=%d\n", i + 1, getpid(), getpgrp());
                
                // Do some work
                sleep(2);
                
                printf("Child %d finished\n", i + 1);
                exit(EXIT_SUCCESS);
            } else if (child < 0) {
                perror("fork");
            }
        }
        
        // Group leader waits for all children
        for (int i = 0; i < 3; i++) {
            wait(NULL);
        }
        
        printf("Group leader finished\n");
        exit(EXIT_SUCCESS);
    } else if (group_leader > 0) {
        // Parent process
        printf("Parent created group leader %d\n", group_leader);
        
        // Wait a bit
        sleep(1);
        
        // Send signal to entire process group
        printf("Parent sending SIGUSR1 to process group %d\n", group_leader);
        kill(-group_leader, SIGUSR1);
        
        // Wait for group leader
        waitpid(group_leader, NULL, 0);
        
        printf("Process group finished\n");
    } else {
        perror("fork");
    }
}

// Signal masking demonstration
void demonstrate_signal_masking() {
    printf("\n=== Signal Masking Demonstration ===\n");
    
    sigset_t mask, old_mask;
    
    // Create mask with SIGINT and SIGTERM
    sigemptyset(&mask);
    sigaddset(&mask, SIGINT);
    sigaddset(&mask, SIGTERM);
    
    // Block signals
    printf("Blocking SIGINT and SIGTERM...\n");
    sigprocmask(SIG_BLOCK, &mask, &old_mask);
    
    // Try to send signals (they will be blocked)
    printf("Sending blocked signals...\n");
    raise(SIGINT);
    raise(SIGTERM);
    printf("Signals sent but blocked\n");
    
    // Do some work
    printf("Working while signals are blocked...\n");
    sleep(2);
    
    // Unblock signals
    printf("Unblocking signals...\n");
    sigprocmask(SIG_UNBLOCK, &mask, NULL);
    
    // Wait for signals to be delivered
    printf("Waiting for signal delivery...\n");
    sleep(2);
    
    // Restore original mask
    sigprocmask(SIG_SETMASK, &old_mask, NULL);
}

// Interactive signal demonstration
void interactive_signal_demo() {
    printf("\n=== Interactive Signal Demo ===\n");
    printf("This process will handle various signals.\n");
    printf("Try sending signals from another terminal:\n");
    printf("  kill -USR1 %d  (Send SIGUSR1)\n", getpid());
    printf("  kill -USR2 %d  (Send SIGUSR2)\n", getpid());
    printf("  kill -TERM %d  (Send SIGTERM)\n", getpid());
    printf("  Press Ctrl+C (Send SIGINT)\n");
    printf("The program will run for 30 seconds or until interrupted.\n");
    
    // Run for 30 seconds
    for (int i = 0; i < 30 && signal_received == 0; i++) {
        printf("Running... %d/30 seconds\n", i + 1);
        sleep(1);
    }
    
    if (signal_received != 0) {
        printf("Signal %d received, exiting...\n", signal_received);
    } else {
        printf("Time's up!\n");
    }
}

// Main test function
void test_signal_handling() {
    printf("=== Signal Handling and Process Monitoring ===\n");
    
    // Setup signal handlers
    setup_signal_handlers();
    
    // Demonstrate signal sets
    demonstrate_signal_sets();
    
    // Demonstrate alarms
    demonstrate_alarms();
    
    // Create signal child
    create_signal_child();
    
    // Process group demonstration
    demonstrate_process_groups();
    
    // Signal masking demonstration
    demonstrate_signal_masking();
    
    // Monitor this process
    printf("\n=== Self-Monitoring ===\n");
    monitor_process(getpid());
    
    // Interactive demo
    interactive_signal_demo();
}

int main() {
    test_signal_handling();
    
    printf("\n=== Signal handling demo completed ===\n");
    
    return 0;
}
