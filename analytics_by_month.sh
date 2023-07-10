#!/bin/bash
END=30
for ((i=0;i<=END;i++)); do
	php analytics_testing.php $i
done
